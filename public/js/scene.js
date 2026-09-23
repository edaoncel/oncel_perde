import * as THREE from 'three/webgpu'
import {
  uniform,
  float,
  vec2,
  vec4,
  color,
  uv,
  mix,
  pass,
  mrt,
  output,
  normalView,
  diffuseColor,
  velocity,
  add,
  directionToColor,
  colorToDirection,
  sample,
  metalness,
  roughness,
  positionWorld,
  fract,
  abs,
  max,
  step,
  convertToTexture,
} from 'three/tsl'
import { ssgi } from 'three/addons/tsl/display/SSGINode.js'
import { ssr } from 'three/addons/tsl/display/SSRNode.js'
import { traa } from 'three/addons/tsl/display/TRAANode.js'
import { gaussianBlur } from 'three/addons/tsl/display/GaussianBlurNode.js'
import { OrbitControls } from 'three/addons/controls/OrbitControls.js'
import { TransformControls } from 'three/addons/controls/TransformControls.js'
import Stats from 'stats-gl'
import { TTFLoader } from 'three/addons/loaders/TTFLoader.js'
import { Font } from 'three/addons/loaders/FontLoader.js'
import { TextGeometry } from 'three/addons/geometries/TextGeometry.js'
import { WaterPlane } from './WaterPlane.js'
import { WaterCaustics } from './WaterCaustics.js'
import * as easings from 'eases-jsnext'

// ─── Params ─────────────────────────────────────────────────────────────────
const params = {
  // Camera
  fov: 55,
  cameraEase: 'quadInOut',
  cameraTransitionDuration: 3,
  blur: 0,
  // Lighting
  sunColor: '#ffb8e0',
  sunIntensity: 2.5,
  ambientColor: '#c090d0',
  ambientIntensity: 0.6,
  exposure: 1.2,
  // Fog
  fogEnabled: false,
  fogColor: '#d488c4',
  fogDensity: 0.012,
  // Buildings
  buildingColor: '#f0b2a3',
  sphereColor: '#f5de8a',
  groundColor: '#d2bea7',
  causticColor: '#ffffff',
  floorColor: '#e8ddd5',
  floorGroutColor: '#c8cbd0',
  bigSphereEmissiveColor: '#eb961e',
  bigSphereEmissiveIntensity: 3,
  fresnelBias: 0.1,
  fresnelPower: 2.0,
  fresnelScale: 1.0,
  floorTileSize: 0.4,
  causticStrength: 1.8,
  causticShadowInfluence: 1.0,
  causticHeightMultiplier: 8,
  // Sky
  skyTopColor: '#9387ae',
  skyBottomColor: '#c38f8a',
  // Sun Position
  sunX: -26,
  sunY: 4,
  sunZ: -9,
  // Shadows
  shadowEnabled: true,
  shadowRadius: 8,
  shadowBlurSamples: 16,
  shadowBias: -0.001,
  shadowNormalBias: 0.02,
  shadowMapSize: 1024,
  // Debug
  debug: false,
}

// ─── Scene ──────────────────────────────────────────────────────────────────
const scene = new THREE.Scene()
scene.fog = params.fogEnabled ? new THREE.FogExp2(params.fogColor, params.fogDensity) : null

const camera = new THREE.PerspectiveCamera(params.fov, innerWidth / innerHeight, 0.1, 500)
camera.position.set(0, 3, 9)
camera.lookAt(0, 1.5, -10)
window.camera = camera

const renderer = new THREE.WebGPURenderer({
  antialias: false,
  requiredLimits: { maxStorageBuffersInVertexStage: 2, maxColorAttachmentBytesPerSample: 64 },
})
const pixelRatio = 1
renderer.setPixelRatio(pixelRatio)
renderer.setSize(innerWidth, innerHeight)
renderer.shadowMap.enabled = true
renderer.shadowMap.type = THREE.VSMShadowMap
renderer.setClearColor(params.skyTopColor)
renderer.toneMapping = THREE.AgXToneMapping
renderer.toneMappingExposure = params.exposure
renderer.domElement.style.cssText = 'position:fixed;top:0;left:0;z-index:-1;'
document.body.appendChild(renderer.domElement)
await renderer.init()

const skyTopColorU = uniform(new THREE.Color(params.skyTopColor))

// ─── Post-Processing (SSGI + TRAA) ──────────────────────────────────────────
const scenePass = pass(scene, camera)
scenePass.setMRT(
  mrt({
    output: output,
    diffuseColor: diffuseColor,
    normal: directionToColor(normalView),
    velocity: velocity,
    metalrough: vec2(metalness, roughness),
  }),
)

const scenePassColor = scenePass.getTextureNode('output')
const scenePassDiffuse = scenePass.getTextureNode('diffuseColor')
const scenePassDepth = scenePass.getTextureNode('depth')
const scenePassNormal = scenePass.getTextureNode('normal')
const scenePassVelocity = scenePass.getTextureNode('velocity')
const scenePassMetalRough = scenePass.getTextureNode('metalrough')

const sceneNormal = sample((uvCoord) => {
  return colorToDirection(scenePassNormal.sample(uvCoord))
})

const giPass = ssgi(scenePassColor, scenePassDepth, sceneNormal, camera)
giPass.sliceCount.value = 2
giPass.stepCount.value = 6
giPass.radius.value = 5
giPass.expFactor.value = 2
giPass.thickness.value = 0.09
giPass.backfaceLighting.value = 0
giPass.aoIntensity.value = 3.1
giPass.giIntensity.value = 19
giPass.useLinearThickness.value = false
giPass.useScreenSpaceSampling.value = true
giPass.useTemporalFiltering = true
giPass.giEnabled = true
giPass.aoEnabled = true

const gi = giPass.rgb
const ao = giPass.a

// ─── Water ──────────────────────────────────────────────────────────────────
const waterPlane = new WaterPlane(scene, renderer, {
  sizeX: 5,
  sizeZ: 14,
  center: new THREE.Vector3(0, 0.25, -1.8),
  resolution: 256 * 2,
  fresnelBias: params.fresnelBias,
  fresnelPower: params.fresnelPower,
  fresnelScale: params.fresnelScale,
  colliderStrength: 0.005,
})

// ─── SSR ─────────────────────────────────────────────────────────────────────
const ssrPass = ssr(scenePassColor, scenePassDepth, sceneNormal, scenePassMetalRough.r, scenePassMetalRough.g)
ssrPass.quality.value = 0.4
ssrPass.blurQuality.value = 1
ssrPass.maxDistance.value = 60
ssrPass.opacity.value = 1
ssrPass.thickness.value = 0.03
ssrPass.enabled = true

const ssrMasked = mix(skyTopColorU.mul(scenePassMetalRough.r), ssrPass.rgb, ssrPass.a)

// ─── RenderPipeline ─────────────────────────────────────────────────────────
const renderPipeline = new THREE.RenderPipeline(renderer)

// SSGI composites (without SSR)
const compositeGiAo = vec4(add(scenePassColor.rgb.mul(ao), scenePassDiffuse.rgb.mul(gi)), scenePassColor.a)
const compositeGiOnly = vec4(add(scenePassColor.rgb, scenePassDiffuse.rgb.mul(gi)), scenePassColor.a)
const compositeAoOnly = vec4(scenePassColor.rgb.mul(ao), scenePassColor.a)

// SSGI composites (with SSR)
const compositeGiAoSsr = vec4(
  add(scenePassColor.rgb.mul(ao), scenePassDiffuse.rgb.mul(gi)).add(ssrMasked),
  scenePassColor.a,
)
const compositeGiOnlySsr = vec4(add(scenePassColor.rgb, scenePassDiffuse.rgb.mul(gi)).add(ssrMasked), scenePassColor.a)
const compositeAoOnlySsr = vec4(scenePassColor.rgb.mul(ao).add(ssrMasked), scenePassColor.a)
const compositeSsrOnly = vec4(scenePassColor.rgb.add(ssrMasked), scenePassColor.a)

// TRAA variants (without SSR)
const traaGiAo = traa(compositeGiAo, scenePassDepth, scenePassVelocity, camera)
const traaGiOnly = traa(compositeGiOnly, scenePassDepth, scenePassVelocity, camera)
const traaAoOnly = traa(compositeAoOnly, scenePassDepth, scenePassVelocity, camera)

// TRAA variants (with SSR)
const traaGiAoSsr = traa(compositeGiAoSsr, scenePassDepth, scenePassVelocity, camera)
const traaGiOnlySsr = traa(compositeGiOnlySsr, scenePassDepth, scenePassVelocity, camera)
const traaAoOnlySsr = traa(compositeAoOnlySsr, scenePassDepth, scenePassVelocity, camera)
const traaSsrOnly = traa(compositeSsrOnly, scenePassDepth, scenePassVelocity, camera)

// ─── Gaussian Blur ──────────────────────────────────────────────────────────
const blurDirectionU = uniform(params.blur * 10)
const blurPass = gaussianBlur(traaGiAoSsr, blurDirectionU, 10)

renderPipeline.outputNode = traaGiAoSsr

// Invisible overlay to capture pointer events for dragging in debug mode
const debugOverlay = document.createElement('div')
debugOverlay.style.cssText = 'position:fixed;inset:0;z-index:1;display:none;'
document.body.appendChild(debugOverlay)

const controls = new OrbitControls(camera, debugOverlay)
controls.enableDamping = true
controls.target.set(0, 1.5, -5)
// controls.target.set(0, 1.5, -40)
controls.maxPolarAngle = Math.PI * 0.6
controls.enabled = params.debug

// ─── Uniforms ───────────────────────────────────────────────────────────────
const buildingColorU = uniform(new THREE.Color(params.buildingColor))
const groundColorU = uniform(new THREE.Color(params.groundColor))
const floorColorU = uniform(new THREE.Color(params.floorColor))
const floorGroutColorU = uniform(new THREE.Color(params.floorGroutColor))
const floorTileSizeU = uniform(params.floorTileSize)
const causticStrengthU = uniform(params.causticStrength)
const causticHeightMultiplierU = uniform(params.causticHeightMultiplier)
const causticColorU = uniform(new THREE.Color(params.causticColor))
const bigSphereEmissiveColorU = uniform(new THREE.Color(params.bigSphereEmissiveColor))
const bigSphereEmissiveIntensityU = uniform(params.bigSphereEmissiveIntensity)
const lightDirU = uniform(new THREE.Vector3(params.sunX, params.sunY, params.sunZ).normalize().negate())

// ─── Lighting ───────────────────────────────────────────────────────────────
const sunLight = new THREE.DirectionalLight(params.sunColor, params.sunIntensity)
sunLight.position.set(params.sunX, params.sunY, params.sunZ)
sunLight.castShadow = params.shadowEnabled
sunLight.shadow.mapSize.width = params.shadowMapSize
sunLight.shadow.mapSize.height = params.shadowMapSize
sunLight.shadow.camera.near = 0.1
sunLight.shadow.camera.far = 40
sunLight.shadow.camera.left = -45
sunLight.shadow.camera.right = 10
sunLight.shadow.camera.top = 30
sunLight.shadow.camera.bottom = -30
sunLight.shadow.radius = params.shadowRadius
sunLight.shadow.blurSamples = params.shadowBlurSamples
sunLight.shadow.bias = params.shadowBias
sunLight.shadow.normalBias = params.shadowNormalBias
scene.add(sunLight)

const shadowHelper = new THREE.CameraHelper(sunLight.shadow.camera)
scene.add(shadowHelper)

const ambientLight = new THREE.AmbientLight(params.ambientColor, params.ambientIntensity)
scene.add(ambientLight)

// ─── Sky Gradient (background plane) ────────────────────────────────────────
const skyBottomColorU = uniform(new THREE.Color(params.skyBottomColor))
const skyHeight = 30
const skyGeo = new THREE.PlaneGeometry(120, skyHeight)
const skyMat = new THREE.MeshBasicNodeMaterial({ fog: false })
skyMat.colorNode = mix(skyBottomColorU, skyTopColorU, uv().y)
const skyMesh = new THREE.Mesh(skyGeo, skyMat)
skyMesh.position.set(0, skyHeight / 2, -50)
scene.add(skyMesh)

// ─── Building Material ──────────────────────────────────────────────────────
const buildingMat = new THREE.MeshStandardNodeMaterial()
buildingMat.colorNode = buildingColorU
buildingMat.roughnessNode = float(0.85)
buildingMat.metalnessNode = float(0.0)

// ─── Architecture ───────────────────────────────────────────────────────────

// Boundary walls with arches (left and right)
{
  const wallH = 8
  const wallThickness = 3.5
  const archRadius = 2.2
  const archStraight = 2.5
  const segmentLen = 8
  const distanceFromCenter = 7
  for (const { wallX, rotY } of [
    { wallX: -distanceFromCenter, rotY: Math.PI / 2 },
    { wallX: distanceFromCenter, rotY: -Math.PI / 2 },
  ]) {
    const sign = Math.sign(wallX)
    for (let z = 5; z >= -40; z -= segmentLen) {
      const wallShape = new THREE.Shape()
      wallShape.moveTo(0, 0)
      wallShape.lineTo(segmentLen, 0)
      wallShape.lineTo(segmentLen, wallH)
      wallShape.lineTo(0, wallH)
      wallShape.lineTo(0, 0)

      const hole = new THREE.Path()
      const cx = segmentLen / 2
      hole.moveTo(cx - archRadius, 0)
      hole.lineTo(cx - archRadius, archStraight)
      hole.absarc(cx, archStraight, archRadius, Math.PI, 0, true)
      hole.lineTo(cx + archRadius, 0)
      hole.lineTo(cx - archRadius, 0)
      wallShape.holes.push(hole)

      const geo = new THREE.ExtrudeGeometry(wallShape, { depth: wallThickness, bevelEnabled: false, curveSegments: 16 })
      const mesh = new THREE.Mesh(geo, buildingMat)
      mesh.rotation.y = rotY
      mesh.position.set(wallX + (sign * wallThickness) / 2, -0.1, sign < 0 ? z : z - segmentLen)
      mesh.castShadow = true
      mesh.receiveShadow = true
      scene.add(mesh)
    }
  }
}

// ─── Right side building details ────────────────────────────────────────────

// Floor with water pool indentation
{
  const floorMat = new THREE.MeshStandardNodeMaterial()
  // Procedural square tile pattern using world position
  const worldPos = positionWorld.xz.div(floorTileSizeU)
  const tileUV = fract(worldPos)
  // Distance from tile edge (0 at edge, 0.5 at center)
  const edgeDist = abs(tileUV.sub(0.5))
  const grout = step(0.48, max(edgeDist.x, edgeDist.y))
  floorMat.colorNode = mix(floorColorU, floorGroutColorU, grout)
  floorMat.roughnessNode = float(0.85)
  floorMat.metalnessNode = float(0.0)

  const floorW = 17.5 // spans to outer wall edges at x=±8.75
  const floorD = 50 // z from +7 to -43 (back arch)
  // Derive pool cutout from water plane geometry
  const { width: poolW, height: poolD } = waterPlane.mesh.geometry.parameters
  const waterCenter = waterPlane.mesh.position
  // With rotation.x=-PI/2, local +Y → world -Z
  // position.z=7, so localY = 7 - worldZ
  const poolOffsetY = 7 - (waterCenter.z + poolD / 2)

  const shape = new THREE.Shape()
  shape.moveTo(-floorW / 2, 0)
  shape.lineTo(floorW / 2, 0)
  shape.lineTo(floorW / 2, floorD)
  shape.lineTo(-floorW / 2, floorD)
  shape.closePath()

  const hole = new THREE.Path()
  hole.moveTo(waterCenter.x - poolW / 2, poolOffsetY)
  hole.lineTo(waterCenter.x + poolW / 2, poolOffsetY)
  hole.lineTo(waterCenter.x + poolW / 2, poolOffsetY + poolD)
  hole.lineTo(waterCenter.x - poolW / 2, poolOffsetY + poolD)
  hole.closePath()
  shape.holes.push(hole)

  const geo = new THREE.ExtrudeGeometry(shape, { depth: 0.4, bevelEnabled: false })
  const floor = new THREE.Mesh(geo, floorMat)
  floor.rotation.x = -Math.PI / 2
  floor.position.set(0, 0, 7)
  floor.receiveShadow = true
  floor.castShadow = true
  scene.add(floor)
}

// Steps from platform to pool
// for (let i = 0; i < 3; i++) {
//   addBox(3, 0.25, 1, 4.5, 0.1 - i * 0.15, 5.5 + i * 1)
// }

// ─── Front arch wall ────────────────────────────────────────────────────────
{
  const wallW = 22
  const wallH = 13
  const archRadius = 3.8
  const archStraight = 7

  const wallShape = new THREE.Shape()
  wallShape.moveTo(-wallW / 2, 0)
  wallShape.lineTo(wallW / 2, 0)
  wallShape.lineTo(wallW / 2, wallH)
  wallShape.lineTo(-wallW / 2, wallH)
  wallShape.lineTo(-wallW / 2, 0)

  const hole = new THREE.Path()
  hole.moveTo(-archRadius, 0)
  hole.lineTo(-archRadius, archStraight)
  hole.absarc(0, archStraight, archRadius, Math.PI, 0, true)
  hole.lineTo(archRadius, 0)
  hole.lineTo(-archRadius, 0)
  wallShape.holes.push(hole)

  const archWallGeo = new THREE.ExtrudeGeometry(wallShape, { depth: 0.8, bevelEnabled: false, curveSegments: 32 })
  const archWall = new THREE.Mesh(archWallGeo, buildingMat)
  //   archWall.position.set(0, -0.1, -11.4)
  archWall.position.set(0, -0.1, -19.5)
  archWall.castShadow = true
  archWall.receiveShadow = true
  scene.add(archWall)
}

// ─── Back arch frame (double arch) ──────────────────────────────────────────
{
  const outerRadius = 5.3
  const innerRadius = 3
  const straight = 8

  // Outer arch shape
  const shape = new THREE.Shape()
  shape.moveTo(-outerRadius, 0)
  shape.lineTo(-outerRadius, straight)
  shape.absarc(0, straight, outerRadius, Math.PI, 0, true)
  shape.lineTo(outerRadius, 0)
  shape.lineTo(-outerRadius, 0)

  // Inner arch as hole
  const hole = new THREE.Path()
  hole.moveTo(-innerRadius, 0)
  hole.lineTo(-innerRadius, straight)
  hole.absarc(0, straight, innerRadius, Math.PI, 0, true)
  hole.lineTo(innerRadius, 0)
  hole.lineTo(-innerRadius, 0)
  shape.holes.push(hole)

  const geo = new THREE.ExtrudeGeometry(shape, { depth: 0.8, bevelEnabled: false, curveSegments: 32 })
  const archFrameMat = new THREE.MeshStandardNodeMaterial()
  archFrameMat.colorNode = buildingColorU
  archFrameMat.roughnessNode = float(0.85)
  archFrameMat.metalnessNode = float(0.0)
  archFrameMat.side = THREE.DoubleSide
  const archFrame = new THREE.Mesh(geo, archFrameMat)
  //   archFrame.position.set(0, 0, -27.5)
  archFrame.position.set(0, 0, -43)
  archFrame.castShadow = true
  archFrame.receiveShadow = true
  scene.add(archFrame)
}

// ─── Scenography (back half of the set) ─────────────────────────────────────

// Columns
{
  const columnGeo = new THREE.CylinderGeometry(0.35, 0.4, 5, 16)
  const capGeo = new THREE.CylinderGeometry(0.55, 0.55, 0.2, 16)
  const baseGeo = new THREE.CylinderGeometry(0.55, 0.55, 0.3, 16)
  const columnX = 5.2
  const columnPositions = [
    [-columnX, -11],
    [columnX, -11],
    [-columnX, -27],
    [columnX, -27],
    [-columnX, -35],
    [columnX, -35],
  ]

  for (const [cx, cz] of columnPositions) {
    const column = new THREE.Mesh(columnGeo, buildingMat)
    column.position.set(cx, 2.5, cz)
    column.castShadow = true
    column.receiveShadow = true
    scene.add(column)

    const base = new THREE.Mesh(baseGeo, buildingMat)
    base.position.set(cx, 0.15, cz)
    base.castShadow = true
    base.receiveShadow = true
    scene.add(base)

    const cap = new THREE.Mesh(capGeo, buildingMat)
    cap.position.set(cx, 5.1, cz)
    cap.castShadow = true
    cap.receiveShadow = true
    scene.add(cap)
  }
}

// Decorative spheres on pedestals
{
  const pedestalGeo = new THREE.BoxGeometry(0.7, 1.2, 0.7)
  const sphereDecGeo = new THREE.IcosahedronGeometry(0.45, 3)

  const spherePositions = [[0, -40]]

  const sphereMat = new THREE.MeshStandardNodeMaterial()
  const sphereColorU = uniform(new THREE.Color(params.sphereColor))
  sphereMat.colorNode = sphereColorU
  // sphereMat.emissiveNode = sphereColorU
  sphereMat.roughnessNode = float(0.7)
  sphereMat.metalnessNode = float(0)

  for (const [sx, sz] of spherePositions) {
    const pedestal = new THREE.Mesh(pedestalGeo, buildingMat)
    pedestal.position.set(sx, 0.6, sz)
    pedestal.castShadow = true
    pedestal.receiveShadow = true
    scene.add(pedestal)

    const sph = new THREE.Mesh(sphereDecGeo, sphereMat)
    sph.position.set(sx, 1.65, sz)
    sph.castShadow = true
    sph.receiveShadow = true
    scene.add(sph)
  }
}

// Freestanding arches
{
  function createArch(x, z, scale = 1, rotY = 0) {
    const outerR = 1.2 * scale
    const innerR = 0.9 * scale
    const straight = 2.5 * scale
    const depth = 0.4 * scale

    const shape = new THREE.Shape()
    shape.moveTo(-outerR, 0)
    shape.lineTo(-outerR, straight)
    shape.absarc(0, straight, outerR, Math.PI, 0, true)
    shape.lineTo(outerR, 0)
    shape.lineTo(innerR, 0)
    shape.lineTo(innerR, straight)
    shape.absarc(0, straight, innerR, 0, Math.PI, false)
    shape.lineTo(-innerR, 0)
    shape.closePath()

    const geo = new THREE.ExtrudeGeometry(shape, { depth, bevelEnabled: false, curveSegments: 16 })
    const mesh = new THREE.Mesh(geo, buildingMat)
    mesh.position.set(x, 0, z + depth / 2)
    mesh.rotation.y = rotY
    mesh.castShadow = true
    mesh.receiveShadow = true
    scene.add(mesh)
  }

  createArch(0, -40, 1.3, 0)
}

// Stairs going up to the right side
{
  const stepMat = buildingMat
  const stepCount = 8
  const stepH = 0.25
  const stepD = 0.4
  const stepW = 2.5

  const rightZ = -15

  // Right-side staircase at z=-14, going from center toward the right wall
  for (let i = 0; i < stepCount; i++) {
    const stepGeo = new THREE.BoxGeometry(stepD, stepH * (i + 1), stepW)
    const step = new THREE.Mesh(stepGeo, stepMat)
    step.position.set(2.3 + i * stepD, (stepH * (i + 1)) / 2, rightZ)
    step.castShadow = true
    step.receiveShadow = true
    scene.add(step)
  }

  // Landing platform at the top
  const landingGeo = new THREE.BoxGeometry(3.5, stepH * stepCount, 4.5)
  const landing = new THREE.Mesh(landingGeo, stepMat)
  landing.position.set(7, (stepH * stepCount) / 2, rightZ)
  landing.castShadow = true
  landing.receiveShadow = true
  scene.add(landing)

  const leftZ = -31

  // Left-side staircase at z=-33, going toward the left wall
  for (let i = 0; i < stepCount; i++) {
    const stepGeo = new THREE.BoxGeometry(stepD, stepH * (i + 1), stepW)
    const step = new THREE.Mesh(stepGeo, stepMat)
    step.position.set(-2.3 - i * stepD, (stepH * (i + 1)) / 2, leftZ)
    step.castShadow = true
    step.receiveShadow = true
    scene.add(step)
  }

  // Landing platform at the top (left)
  const landingGeo2 = new THREE.BoxGeometry(3.5, stepH * stepCount, 4.5)
  const landing2 = new THREE.Mesh(landingGeo2, stepMat)
  landing2.position.set(-7, (stepH * stepCount) / 2, leftZ)
  landing2.castShadow = true
  landing2.receiveShadow = true
  scene.add(landing2)
}

// Big sphere half-buried in the ground
{
  const bigSphereMat = new THREE.MeshStandardNodeMaterial()
  bigSphereMat.colorNode = buildingColorU
  bigSphereMat.emissiveNode = bigSphereEmissiveColorU.mul(bigSphereEmissiveIntensityU)
  bigSphereMat.roughnessNode = float(1)
  bigSphereMat.metalnessNode = float(0)
  const bigSphereGeo = new THREE.IcosahedronGeometry(1.8, 5)
  const bigSphere = new THREE.Mesh(bigSphereGeo, bigSphereMat)
  bigSphere.position.set(4.5, 0, -23)
  bigSphere.castShadow = false
  bigSphere.receiveShadow = false
  scene.add(bigSphere)
}

// ─── Lounge Chairs ──────────────────────────────────────────────────────────
const woodMat = new THREE.MeshStandardNodeMaterial()
woodMat.colorNode = color(0x8b5e3c)
woodMat.roughnessNode = float(0.8)

const cushionMat = new THREE.MeshStandardNodeMaterial()
cushionMat.colorNode = color(0xf0e8e0)
cushionMat.roughnessNode = float(0.6)

function createLoungeChair(x, z, rotY) {
  const group = new THREE.Group()

  // Frame (base)
  const frameGeo = new THREE.BoxGeometry(0.8, 0.08, 1.9)
  const frame = new THREE.Mesh(frameGeo, woodMat)
  frame.position.y = 0.35
  frame.position.z = -0.1
  group.add(frame)

  // Legs
  const legGeo = new THREE.BoxGeometry(0.08, 0.35, 0.08)
  const positions = [
    [-0.35, 0.175, -0.85],
    [0.35, 0.175, -0.85],
    [-0.35, 0.175, 0.85],
    [0.35, 0.175, 0.85],
  ]
  for (const [lx, ly, lz] of positions) {
    const leg = new THREE.Mesh(legGeo, woodMat)
    leg.position.set(lx, ly, lz)
    leg.position.z -= 0.1
    group.add(leg)
  }

  // Cushion (seat)
  const cushionGeo = new THREE.BoxGeometry(0.75, 0.12, 1.4)
  const cushion = new THREE.Mesh(cushionGeo, cushionMat)
  cushion.position.set(0, 0.45, 0.1)
  group.add(cushion)

  // Back rest (angled)
  const backGeo = new THREE.BoxGeometry(0.75, 0.1, 0.7)
  const back = new THREE.Mesh(backGeo, cushionMat)
  back.position.set(0, 0.5, -0.7)
  back.rotation.x = 0.4
  group.add(back)

  group.position.set(x, 0.2, z)
  group.rotation.y = rotY
  scene.add(group)
  group.traverse((child) => {
    if (child.isMesh) {
      child.castShadow = true
      child.receiveShadow = true
    }
  })
}

createLoungeChair(4.5, 0, (-Math.PI / 2) * 0.8)
createLoungeChair(5.2, 1.5, (-Math.PI / 2) * 0.8)

// ─── Ground plane ────────────────────────────────────────────
const groundMat = new THREE.MeshStandardNodeMaterial()
groundMat.colorNode = groundColorU
groundMat.roughnessNode = float(0.9)
const groundGeo = new THREE.PlaneGeometry(120, 80)
const ground = new THREE.Mesh(groundGeo, groundMat)
ground.rotation.x = -Math.PI / 2
ground.position.set(0, -0.01, -30)
// ground.receiveShadow = true
scene.add(ground)

// ─── Caustics (deformed water mesh projected to floor) ──────────────────────
const waterCaustics = new WaterCaustics(scene, waterPlane, {
  lightDir: lightDirU,
  floorY: waterPlane.mesh.position.y - 0.24,
  causticColor: causticColorU,
  causticStrength: causticStrengthU,
  baseColor: groundColorU,
  heightMultiplier: causticHeightMultiplierU,
})

// ─── Text/Collider Z offset (declared early so GUI can reference it) ─────
let _sphereRefOffsetZ = -6.5 // colliderSphere.position.z - camera.position.z

// ─── Sphere Collider ────────────────────────────────────────────────────────
let colliderRadius = 1.5
const colliderSphere = new THREE.Mesh(
  new THREE.SphereGeometry(colliderRadius, 16, 16),
  new THREE.MeshStandardNodeMaterial({
    color: '#ff6644',
    roughness: 0.4,
    metalness: 0.2,
    transparent: true,
    opacity: 0.6,
  }),
)
colliderSphere.position.set(0, 4, 2.5)
scene.add(colliderSphere)

// ─── 3D Text ─────────────────────────────────────────────────────────────────
const ttfLoader = new TTFLoader()
const fontData = await new Promise((resolve) =>
  ttfLoader.load(
    'https://fonts.gstatic.com/s/playfairdisplay/v40/nuFRD-vYSZviVYUb_rj3ij__anPXDTnCjmHKM4nYO7KN_qiTbtY.ttf',
    resolve,
  ),
)
const textFont = new Font(fontData)
const textMat = new THREE.MeshBasicNodeMaterial()
textMat.colorNode = color(0xffffff)

const textGroup = new THREE.Group()
const textBaseSize = 1

function createCenteredText(str, yOffset) {
  const geo = new TextGeometry(str, {
    font: textFont,
    size: textBaseSize,
    depth: 0.01,
    curveSegments: 8,
  })
  geo.computeBoundingBox()
  const width = geo.boundingBox.max.x - geo.boundingBox.min.x
  geo.translate(-width / 2, 0, 0)
  const mesh = new THREE.Mesh(geo, textMat)
  mesh.position.y = yOffset
  mesh.castShadow = true
  mesh.receiveShadow = true
  return mesh
}

const textLineHeight = textBaseSize * 1.15
const line1 = createCenteredText('The architecture', textLineHeight)
const line2 = createCenteredText('of silence', 0)
textGroup.add(line1, line2)
textGroup.position.z = colliderSphere.position.z

// Compute base bounding box for scaling, and vertically center the text children
const textBox = new THREE.Box3().setFromObject(textGroup)
const baseTextWidth = textBox.max.x - textBox.min.x
// Shift children down by ~1 line
line1.position.y -= textBaseSize * 0.9
line2.position.y -= textBaseSize * 1.3

camera.add(textGroup)
scene.add(camera)

const transformControls = new TransformControls(camera, debugOverlay)
transformControls.attach(colliderSphere)
transformControls.addEventListener('dragging-changed', (event) => {
  controls.enabled = !event.value && params.debug
})
const transformHelper = transformControls.getHelper()
scene.add(transformHelper)

// ─── Stats ──────────────────────────────────────────────────────────────────
const stats = new Stats({ trackGPU: true, trackCPT: true })
document.body.appendChild(stats.dom)
stats.init(renderer)

// ─── Custom GUI ─────────────────────────────────────────────────────────────
const guiPanel = document.getElementById('guiPanel')
const guiPanelHeader = document.getElementById('guiPanelHeader')
const guiPanelBody = document.getElementById('guiPanelBody')

guiPanelHeader.addEventListener('click', () => {
  guiPanel.classList.toggle('collapsed')
})

// GUI builder helpers
function guiFolder(name) {
  const folder = document.createElement('div')
  folder.className = 'gui-folder closed'
  const header = document.createElement('div')
  header.className = 'gui-folder-header'
  header.innerHTML = `<span class="folder-chevron">▼</span>${name}`
  header.addEventListener('click', () => folder.classList.toggle('closed'))
  const body = document.createElement('div')
  body.className = 'gui-folder-body'
  folder.appendChild(header)
  folder.appendChild(body)
  guiPanelBody.appendChild(folder)
  return body
}

function guiSlider(parent, label, value, min, max, step, onChange) {
  const row = document.createElement('div')
  row.className = 'gui-row'
  const lbl = document.createElement('label')
  lbl.textContent = label
  const ctrl = document.createElement('div')
  ctrl.className = 'gui-control'
  const input = document.createElement('input')
  input.type = 'range'
  input.min = min
  input.max = max
  input.step = step
  input.value = value
  const val = document.createElement('span')
  val.className = 'gui-value'
  val.textContent = Number(value).toFixed(step < 0.01 ? 4 : step < 1 ? 2 : 0)
  input.addEventListener('input', () => {
    const v = parseFloat(input.value)
    val.textContent = v.toFixed(step < 0.01 ? 4 : step < 1 ? 2 : 0)
    onChange(v)
  })
  ctrl.appendChild(input)
  row.appendChild(lbl)
  row.appendChild(ctrl)
  row.appendChild(val)
  parent.appendChild(row)
  return input
}

function guiColor(parent, label, value, onChange) {
  const row = document.createElement('div')
  row.className = 'gui-row'
  const lbl = document.createElement('label')
  lbl.textContent = label
  const ctrl = document.createElement('div')
  ctrl.className = 'gui-control'
  const input = document.createElement('input')
  input.type = 'color'
  input.value = value
  input.addEventListener('input', () => onChange(input.value))
  ctrl.appendChild(input)
  row.appendChild(lbl)
  row.appendChild(ctrl)
  parent.appendChild(row)
  return input
}

function guiCheckbox(parent, label, value, onChange) {
  const row = document.createElement('div')
  row.className = 'gui-row'
  const lbl = document.createElement('label')
  lbl.textContent = label
  const ctrl = document.createElement('div')
  ctrl.className = 'gui-control'
  const input = document.createElement('input')
  input.type = 'checkbox'
  input.checked = value
  input.addEventListener('change', () => onChange(input.checked))
  ctrl.appendChild(input)
  row.appendChild(lbl)
  row.appendChild(ctrl)
  parent.appendChild(row)
  return input
}

function guiSelect(parent, label, options, value, onChange) {
  const row = document.createElement('div')
  row.className = 'gui-row'
  const lbl = document.createElement('label')
  lbl.textContent = label
  const ctrl = document.createElement('div')
  ctrl.className = 'gui-control'
  const sel = document.createElement('select')
  for (const opt of options) {
    const o = document.createElement('option')
    o.value = opt
    o.textContent = opt
    if (opt == value) o.selected = true
    sel.appendChild(o)
  }
  sel.addEventListener('change', () => onChange(sel.value))
  ctrl.appendChild(sel)
  row.appendChild(lbl)
  row.appendChild(ctrl)
  parent.appendChild(row)
  return sel
}

// ── Camera folder
const cameraF = guiFolder('Camera')
guiSlider(cameraF, 'FOV', params.fov, 20, 120, 1, (v) => { params.fov = v; camera.fov = v; camera.updateProjectionMatrix() })
guiSelect(cameraF, 'Easing', Object.keys(easings).filter((k) => typeof easings[k] === 'function'), params.cameraEase, (v) => { params.cameraEase = v })
guiSlider(cameraF, 'Duration', params.cameraTransitionDuration, 0.5, 6, 0.1, (v) => { params.cameraTransitionDuration = v })
guiSlider(cameraF, 'Blur', params.blur, 0, 1, 0.01, (v) => { params.blur = v; blurDirectionU.value = v * 10 })

// ── Lights folder
const lightsF = guiFolder('Lights')
guiColor(lightsF, 'Sun Color', params.sunColor, (v) => { params.sunColor = v; sunLight.color.set(v) })
guiSlider(lightsF, 'Sun Intensity', params.sunIntensity, 0, 10, 0.1, (v) => { params.sunIntensity = v; sunLight.intensity = v })
guiSlider(lightsF, 'Sun X', params.sunX, -50, 50, 0.5, (v) => { params.sunX = v; sunLight.position.x = v; lightDirU.value.copy(sunLight.position).normalize().negate() })
guiSlider(lightsF, 'Sun Y', params.sunY, 0, 100, 0.5, (v) => { params.sunY = v; sunLight.position.y = v; lightDirU.value.copy(sunLight.position).normalize().negate() })
guiSlider(lightsF, 'Sun Z', params.sunZ, -50, 50, 0.5, (v) => { params.sunZ = v; sunLight.position.z = v; lightDirU.value.copy(sunLight.position).normalize().negate() })
guiColor(lightsF, 'Ambient Color', params.ambientColor, (v) => { params.ambientColor = v; ambientLight.color.set(v) })
guiSlider(lightsF, 'Ambient Intensity', params.ambientIntensity, 0, 3, 0.05, (v) => { params.ambientIntensity = v; ambientLight.intensity = v })
guiColor(lightsF, 'Sky Top Color', params.skyTopColor, (v) => { params.skyTopColor = v; skyTopColorU.value.set(v) })
guiColor(lightsF, 'Sky Bottom Color', params.skyBottomColor, (v) => { params.skyBottomColor = v; skyBottomColorU.value.set(v) })
guiSlider(lightsF, 'Exposure', params.exposure, 0.1, 3, 0.05, (v) => { params.exposure = v; renderer.toneMappingExposure = v })

// ── Shadows folder
const shadowF = guiFolder('Shadows')
guiCheckbox(shadowF, 'Enabled', params.shadowEnabled, (v) => { params.shadowEnabled = v; sunLight.castShadow = v })
guiSlider(shadowF, 'Radius', params.shadowRadius, 0, 30, 0.5, (v) => { params.shadowRadius = v; sunLight.shadow.radius = v })
guiSlider(shadowF, 'Blur Samples', params.shadowBlurSamples, 1, 64, 1, (v) => { params.shadowBlurSamples = v; sunLight.shadow.blurSamples = v })
guiSlider(shadowF, 'Bias', params.shadowBias, -0.01, 0.01, 0.0001, (v) => { params.shadowBias = v; sunLight.shadow.bias = v })
guiSlider(shadowF, 'Normal Bias', params.shadowNormalBias, 0, 0.1, 0.001, (v) => { params.shadowNormalBias = v; sunLight.shadow.normalBias = v })
guiSelect(shadowF, 'Map Size', [1024, 2048], params.shadowMapSize, (v) => { const s = parseInt(v); params.shadowMapSize = s; sunLight.shadow.mapSize.width = s; sunLight.shadow.mapSize.height = s; sunLight.shadow.dispose(); sunLight.shadow.map = null })

// ── SSGI folder
const ssgiF = guiFolder('SSGI')

function updateOutputPipeline() {
  const { giEnabled, aoEnabled } = giPass
  const { enabled: ssrEnabled } = ssrPass
  let node

  if (giEnabled && aoEnabled) {
    node = ssrEnabled ? traaGiAoSsr : traaGiAo
  } else if (giEnabled) {
    node = ssrEnabled ? traaGiOnlySsr : traaGiOnly
  } else if (aoEnabled) {
    node = ssrEnabled ? traaAoOnlySsr : traaAoOnly
  } else {
    node = ssrEnabled ? traaSsrOnly : scenePassColor
  }

  blurPass.textureNode = convertToTexture(node)
  renderPipeline.outputNode = blurPass
  renderPipeline.needsUpdate = true
}
updateOutputPipeline()

guiCheckbox(ssgiF, 'GI Enabled', giPass.giEnabled, (v) => { giPass.giEnabled = v; updateOutputPipeline() })
guiCheckbox(ssgiF, 'AO Enabled', giPass.aoEnabled, (v) => { giPass.aoEnabled = v; updateOutputPipeline() })
guiSlider(ssgiF, 'Slice Count', giPass.sliceCount.value, 1, 4, 1, (v) => { giPass.sliceCount.value = v })
guiSlider(ssgiF, 'Step Count', giPass.stepCount.value, 1, 32, 1, (v) => { giPass.stepCount.value = v })
guiSlider(ssgiF, 'Radius', giPass.radius.value, 1, 25, 0.5, (v) => { giPass.radius.value = v })
guiSlider(ssgiF, 'Exp Factor', giPass.expFactor.value, 1, 3, 0.1, (v) => { giPass.expFactor.value = v })
guiSlider(ssgiF, 'Thickness', giPass.thickness.value, 0.01, 10, 0.01, (v) => { giPass.thickness.value = v })
guiSlider(ssgiF, 'Backface Lighting', giPass.backfaceLighting.value, 0, 1, 0.01, (v) => { giPass.backfaceLighting.value = v })
guiSlider(ssgiF, 'AO Intensity', giPass.aoIntensity.value, 0, 4, 0.1, (v) => { giPass.aoIntensity.value = v })
guiSlider(ssgiF, 'GI Intensity', giPass.giIntensity.value, 0, 100, 0.5, (v) => { giPass.giIntensity.value = v })
guiCheckbox(ssgiF, 'Linear Thickness', giPass.useLinearThickness.value, (v) => { giPass.useLinearThickness.value = v })
guiCheckbox(ssgiF, 'Screen-Space Sampling', giPass.useScreenSpaceSampling.value, (v) => { giPass.useScreenSpaceSampling.value = v })

// ── Water folder
const waterF = guiFolder('Water')
guiCheckbox(waterF, 'Enabled', waterPlane.mesh.visible, (v) => { waterPlane.mesh.visible = v })
guiColor(waterF, 'Color', '#' + waterPlane.material.color.getHexString(), (v) => { waterPlane.material.color.set(v) })
guiSlider(waterF, 'Roughness', waterPlane.material.roughness, 0, 1, 0.05, (v) => { waterPlane.material.roughness = v })
guiSlider(waterF, 'Metalness', waterPlane.material.metalness, 0, 1, 0.05, (v) => { waterPlane.material.metalness = v })
guiSlider(waterF, 'Fresnel Bias', params.fresnelBias, 0, 1, 0.01, (v) => { params.fresnelBias = v; waterPlane.uniforms.fresnelBias.value = v })
guiSlider(waterF, 'Fresnel Power', params.fresnelPower, 0.1, 10, 0.1, (v) => { params.fresnelPower = v; waterPlane.uniforms.fresnelPower.value = v })
guiSlider(waterF, 'Fresnel Scale', params.fresnelScale, 0, 3, 0.05, (v) => { params.fresnelScale = v; waterPlane.uniforms.fresnelScale.value = v })
guiSlider(waterF, 'Viscosity', waterPlane.uniforms.viscosity.value, 0, 0.999, 0.001, (v) => { waterPlane.uniforms.viscosity.value = v })
guiSlider(waterF, 'Damping', waterPlane.uniforms.damping.value, 0, 0.3, 0.005, (v) => { waterPlane.uniforms.damping.value = v })
guiSlider(waterF, 'Wave Speed', waterPlane.uniforms.speed.value, 0.1, 1, 0.01, (v) => { waterPlane.uniforms.speed.value = v })
guiSlider(waterF, 'Mouse Deep', waterPlane.uniforms.mouseDeep.value, 0.01, 1, 0.01, (v) => { waterPlane.uniforms.mouseDeep.value = v })
guiSlider(waterF, 'Mouse Size', waterPlane.uniforms.mouseSize.value, 0.05, 1, 0.01, (v) => { waterPlane.uniforms.mouseSize.value = v })
guiSlider(waterF, 'Collider Strength', waterPlane.uniforms.colliderStrength.value, 0, 1, 0.001, (v) => { waterPlane.uniforms.colliderStrength.value = v })
guiSlider(waterF, 'Collider Z Offset', _sphereRefOffsetZ, -20, 5, 0.1, (v) => { _sphereRefOffsetZ = v })
guiSlider(waterF, 'Collider Size', colliderRadius, 0.1, 5, 0.05, (v) => { colliderRadius = v; colliderSphere.scale.setScalar(v / 1.2) })
guiSlider(waterF, 'Noise Amplitude', waterPlane.uniforms.noiseAmplitude.value, 0, 2, 0.001, (v) => { waterPlane.uniforms.noiseAmplitude.value = v })
guiSlider(waterF, 'Noise Frequency', waterPlane.uniforms.noiseFrequency.value, 0.1, 20, 0.1, (v) => { waterPlane.uniforms.noiseFrequency.value = v })
guiSlider(waterF, 'Noise Speed', waterPlane.uniforms.noiseSpeed.value, 0, 3, 0.05, (v) => { waterPlane.uniforms.noiseSpeed.value = v })
guiSlider(waterF, 'Caustic Strength', params.causticStrength, 0, 30, 0.1, (v) => { params.causticStrength = v; causticStrengthU.value = v })
guiColor(waterF, 'Caustic Color', params.causticColor, (v) => { params.causticColor = v; causticColorU.value.set(v) })
guiSlider(waterF, 'Caustic Shadow', params.causticShadowInfluence, 0, 1, 0.01, (v) => { params.causticShadowInfluence = v; waterCaustics.uniforms.shadowInfluence.value = v })
guiSlider(waterF, 'Caustic Height Mult', params.causticHeightMultiplier, 0.1, 20, 0.1, (v) => { params.causticHeightMultiplier = v; causticHeightMultiplierU.value = v })

// ── SSR folder
const ssrF = guiFolder('SSR')
guiCheckbox(ssrF, 'Enabled', ssrPass.enabled, (v) => { ssrPass.enabled = v; updateOutputPipeline() })
guiSlider(ssrF, 'Quality', ssrPass.quality.value, 0, 1, 0.1, (v) => { ssrPass.quality.value = v })
guiSlider(ssrF, 'Blur Quality', ssrPass.blurQuality.value, 1, 3, 1, (v) => { ssrPass.blurQuality.value = v })
guiSlider(ssrF, 'Max Distance', ssrPass.maxDistance.value, 0.1, 100, 0.1, (v) => { ssrPass.maxDistance.value = v })
guiSlider(ssrF, 'Opacity', ssrPass.opacity.value, 0, 1, 0.05, (v) => { ssrPass.opacity.value = v })
guiSlider(ssrF, 'Thickness', ssrPass.thickness.value, 0.001, 0.1, 0.001, (v) => { ssrPass.thickness.value = v })

// ── Fog folder
const fogF = guiFolder('Fog')
guiCheckbox(fogF, 'Enabled', params.fogEnabled, (v) => { params.fogEnabled = v; scene.fog = v ? new THREE.FogExp2(params.fogColor, params.fogDensity) : null })
guiColor(fogF, 'Color', params.fogColor, (v) => { params.fogColor = v; if (scene.fog) { scene.fog.color.set(v); renderer.setClearColor(v) } })
guiSlider(fogF, 'Density', params.fogDensity, 0, 0.05, 0.001, (v) => { params.fogDensity = v; if (scene.fog) scene.fog.density = v })

// ── Buildings folder
const buildingsF = guiFolder('Buildings')
guiColor(buildingsF, 'Color', params.buildingColor, (v) => { params.buildingColor = v; buildingColorU.value.set(v) })
guiColor(buildingsF, 'Sphere', params.sphereColor, (v) => { params.sphereColor = v; sphereColorU.value.set(v) })
guiColor(buildingsF, 'Ground', params.groundColor, (v) => { params.groundColor = v; groundColorU.value.set(v) })
guiColor(buildingsF, 'Floor', params.floorColor, (v) => { params.floorColor = v; floorColorU.value.set(v) })
guiColor(buildingsF, 'Floor Grout', params.floorGroutColor, (v) => { params.floorGroutColor = v; floorGroutColorU.value.set(v) })
guiSlider(buildingsF, 'Floor Tile Size', params.floorTileSize, 0.1, 2, 0.01, (v) => { params.floorTileSize = v; floorTileSizeU.value = v })
guiColor(buildingsF, 'Sphere Emissive', params.bigSphereEmissiveColor, (v) => { params.bigSphereEmissiveColor = v; bigSphereEmissiveColorU.value.set(v) })
guiSlider(buildingsF, 'Sphere Emissive Int.', params.bigSphereEmissiveIntensity, 0, 10, 0.01, (v) => { params.bigSphereEmissiveIntensity = v; bigSphereEmissiveIntensityU.value = v })

function setDebug(enabled) {
  params.debug = enabled
  controls.enabled = enabled
  stats.dom.style.display = enabled ? '' : 'none'
  transformHelper.visible = enabled
  colliderSphere.visible = enabled
  shadowHelper.visible = enabled
  transformControls.enabled = enabled
  debugOverlay.style.display = enabled ? 'block' : 'none'
  silenceH2.style.opacity = enabled ? '1' : '0'
  debugToggleInput.checked = enabled
}

let debugToggleInput
{
  const debugF = guiFolder('Debug')
  debugToggleInput = guiCheckbox(debugF, 'Debug (P)', params.debug, setDebug)
}

window.addEventListener('keydown', (e) => {
  if (e.key === 'p' || e.key === 'P') {
    setDebug(!params.debug)
  }
})

// ─── Text-to-sphere Y binding ────────────────────────────────────────────────
/** @type {HTMLElement} */
const silenceH2 = document.querySelector('.sections .section:first-child h2')

// Apply initial debug state
setDebug(params.debug)
const _projVec = new THREE.Vector3()
const _unprojVec = new THREE.Vector3()
const _unprojVec2 = new THREE.Vector3()
// Reference point that follows the camera Z so the text depth is always valid
const _sphereRefPoint = new THREE.Vector3(colliderSphere.position.x, 0, colliderSphere.position.z)

// ─── Scroll-driven camera Z animation ────────────────────────────────────────
const cameraStartZ = camera.position.z
const cameraMidZ = -14
const cameraEndZ = -31
const controlsStartZ = controls.target.z

// Ease-in-out transition state
let transitionFromCamZ = cameraStartZ
let transitionFromCtrlZ = controlsStartZ
let transitionToCamZ = cameraStartZ
let transitionToCtrlZ = controlsStartZ
let transitionStartTime = 0
let transitionProgress = 1 // start fully settled

function ease(t) {
  return (easings[params.cameraEase] || easings.cubicInOut)(t)
}
let transitionDuration = params.cameraTransitionDuration

const allSections = document.querySelectorAll('.sections .section')
const section3El = allSections[1] // Chapter II "Draped in liquid light" (3rd overall)
const section4El = allSections[2] // Chapter III "Colour of warm stone" (4th overall)
const finaleEl = document.querySelector('.finale')

// Cache document-space positions (independent of scroll), recompute on resize
let section3Top = 0
let section4Top = 0
let finaleTop = 0
function updateSectionOffsets() {
  const scrollY = window.scrollY
  section3Top = section3El.getBoundingClientRect().top + scrollY
  section4Top = section4El.getBoundingClientRect().top + scrollY
  finaleTop = finaleEl.getBoundingClientRect().top + scrollY
}
updateSectionOffsets()

function updateCameraProgress() {
  const viewportCenter = window.scrollY + innerHeight / 2

  let newTargetCamZ
  if (viewportCenter >= section4Top) {
    newTargetCamZ = cameraEndZ
  } else if (viewportCenter >= section3Top) {
    newTargetCamZ = cameraMidZ
  } else {
    newTargetCamZ = cameraStartZ
  }

  if (newTargetCamZ !== transitionToCamZ) {
    transitionFromCamZ = camera.position.z
    transitionFromCtrlZ = controls.target.z
    transitionToCamZ = newTargetCamZ
    transitionToCtrlZ = controlsStartZ + (newTargetCamZ - cameraStartZ)
    transitionStartTime = performance.now() / 1000
    transitionProgress = 0
    transitionDuration = params.cameraTransitionDuration
  }
}
// Blur transition state
let blurFrom = 0
let blurTo = 0
let blurTransitionStart = 0
let blurTransitionProgress = 1

function updateBlurTarget(targetBlur) {
  if (targetBlur === blurTo) return
  blurFrom = params.blur
  blurTo = targetBlur
  blurTransitionStart = performance.now() / 1000
  blurTransitionProgress = 0
}

function updateCameraAndBlurProgress() {
  updateCameraProgress()

  const viewportCenter = window.scrollY + innerHeight / 2
  updateBlurTarget(viewportCenter >= finaleTop ? 0.1 : 0)
}

window.addEventListener('scroll', updateCameraAndBlurProgress, { passive: true })

// ─── Mouse interaction ──────────────────────────────────────────────────────
const mouseNDC = new THREE.Vector2(-Infinity, -Infinity)

window.addEventListener('pointermove', (event) => {
  mouseNDC.set((event.clientX / innerWidth) * 2 - 1, -(event.clientY / innerHeight) * 2 + 1)
})

// ─── Resize ─────────────────────────────────────────────────────────────────
window.addEventListener('resize', () => {
  camera.aspect = innerWidth / innerHeight
  camera.updateProjectionMatrix()
  renderer.setPixelRatio(pixelRatio)
  renderer.setSize(innerWidth, innerHeight)
  updateSectionOffsets()
  updateCameraProgress()
})

// ─── Animate ────────────────────────────────────────────────────────────────
async function animate() {
  controls.update()

  {
    // Keep the reference point at a fixed offset in front of the camera
    _sphereRefPoint.z = camera.position.z + _sphereRefOffsetZ
    colliderSphere.position.z = _sphereRefPoint.z

    _projVec.copy(_sphereRefPoint).project(camera)
    const projZ = _projVec.z

    {
      const rect = silenceH2.getBoundingClientRect()
      const centerNdcX = ((rect.left + rect.width / 2) / innerWidth) * 2 - 1
      const centerNdcY = -((rect.top + rect.height / 2) / innerHeight) * 2 + 1

      // Unproject center of the HTML text at the text's depth
      _unprojVec.set(centerNdcX, centerNdcY, projZ).unproject(camera)
      colliderSphere.position.y = _unprojVec.y
      // Position text in camera-local space
      camera.worldToLocal(_unprojVec)
      textGroup.position.copy(_unprojVec)

      // Scale 3D text to match the HTML text's width
      const leftNdcX = (rect.left / innerWidth) * 2 - 1
      const rightNdcX = ((rect.left + rect.width) / innerWidth) * 2 - 1
      _unprojVec.set(leftNdcX, centerNdcY, projZ).unproject(camera)
      _unprojVec2.set(rightNdcX, centerNdcY, projZ).unproject(camera)
      const targetWidth = _unprojVec2.x - _unprojVec.x
      const scale = targetWidth / baseTextWidth
      textGroup.scale.setScalar(scale)
    }
  }

  // Scroll-driven camera Z with ease-in-out
  if (!params.debug && transitionProgress < 1) {
    transitionProgress = Math.min((performance.now() / 1000 - transitionStartTime) / transitionDuration, 1)
    const eased = ease(transitionProgress)
    camera.position.z = transitionFromCamZ + (transitionToCamZ - transitionFromCamZ) * eased
    controls.target.z = transitionFromCtrlZ + (transitionToCtrlZ - transitionFromCtrlZ) * eased
  }

  // Blur transition
  if (blurTransitionProgress < 1) {
    blurTransitionProgress = Math.min((performance.now() / 1000 - blurTransitionStart) / 0.3, 1)
    const eased = easings.quadOut(blurTransitionProgress)
    const blurValue = blurFrom + (blurTo - blurFrom) * eased
    params.blur = blurValue
    blurDirectionU.value = blurValue * 10
  }

  if (waterPlane.mesh.visible) {
    waterPlane.update(mouseNDC, camera, colliderSphere.position, colliderRadius)
  }

  renderPipeline.render()

  stats.update()
  await renderer.resolveTimestampsAsync('render')
  await renderer.resolveTimestampsAsync('compute')
}
renderer.setAnimationLoop(animate)



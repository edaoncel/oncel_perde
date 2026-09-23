import * as THREE from 'three/webgpu'
import {
  uniform,
  float,
  vec2,
  vec3,
  mix,
  Fn,
  instancedArray,
  instanceIndex,
  vertexIndex,
  globalId,
  int,
  min,
  max,
  length,
  clamp,
  cos,
  select,
  positionLocal,
  positionWorld,
  cameraPosition,
  transformNormalToView,
  mx_fractal_noise_float,
  time,
  normalize,
} from 'three/tsl'

const WORKGROUP_SIZE = 16

export class WaterPlane {
  #renderer
  #mousePosU
  #mouseSpeedU
  #colliderPosU
  #colliderYU
  #colliderRadiusU
  #computeHeightAtoB
  #computeHeightBtoA
  #raycastMesh
  #raycaster
  #pingPong
  #dispatchX
  #dispatchZ

  constructor(
    scene,
    renderer,
    {
      sizeX = 5,
      sizeZ = 5,
      center = new THREE.Vector3(0, 0, 0),
      color = '#9bd2ec',
      metalness = 0.1,
      roughness = 0.15,
      fresnelBias = 0.1,
      fresnelPower = 2.0,
      fresnelScale = 1.0,
      resolution = 128,
      viscosity = 0.6,
      damping = 0,
      speed = 0.97,
      mouseDeep = 0.01,
      mouseSize = 0.61,
      colliderStrength = 0.002,
      noiseAmplitude = 0.117,
      noiseFrequency = 4,
      noiseSpeed = 1.2,
    } = {},
  ) {
    this.#renderer = renderer
    this.center = center
    this.sizeX = sizeX
    this.sizeZ = sizeZ

    // ─── Grid resolution (proportional to world dims, rounded to workgroup size) ─
    const maxDim = Math.max(sizeX, sizeZ)
    const resX = Math.ceil(((sizeX / maxDim) * resolution) / WORKGROUP_SIZE) * WORKGROUP_SIZE
    const resZ = Math.ceil(((sizeZ / maxDim) * resolution) / WORKGROUP_SIZE) * WORKGROUP_SIZE
    this.resX = resX
    this.resZ = resZ
    this.#dispatchX = resX / WORKGROUP_SIZE
    this.#dispatchZ = resZ / WORKGROUP_SIZE

    // ─── Uniforms ──────────────────────────────────────────────────────────────
    const readFromA = uniform(1)
    const mousePosU = uniform(new THREE.Vector2())
    const mouseSpeedU = uniform(new THREE.Vector2())
    const mouseDeepU = uniform(mouseDeep)
    const mouseSizeU = uniform(mouseSize)
    const viscosityU = uniform(viscosity)
    const dampingU = uniform(damping)
    const speedU = uniform(speed)
    const colliderPosU = uniform(new THREE.Vector2())
    const colliderYU = uniform(0)
    const colliderRadiusU = uniform(0)
    const colliderStrengthU = uniform(colliderStrength)
    const noiseAmplitudeU = uniform(noiseAmplitude)
    const noiseFrequencyU = uniform(noiseFrequency)
    const noiseSpeedU = uniform(noiseSpeed)
    const fresnelBiasU = uniform(fresnelBias)
    const fresnelPowerU = uniform(fresnelPower)
    const fresnelScaleU = uniform(fresnelScale)

    this.readFromA = readFromA
    this.#mousePosU = mousePosU
    this.#mouseSpeedU = mouseSpeedU
    this.#colliderPosU = colliderPosU
    this.#colliderYU = colliderYU
    this.#colliderRadiusU = colliderRadiusU

    // Exposed uniforms for GUI binding
    this.uniforms = {
      viscosity: viscosityU,
      damping: dampingU,
      speed: speedU,
      mouseDeep: mouseDeepU,
      mouseSize: mouseSizeU,
      colliderStrength: colliderStrengthU,
      noiseAmplitude: noiseAmplitudeU,
      noiseFrequency: noiseFrequencyU,
      noiseSpeed: noiseSpeedU,
      fresnelBias: fresnelBiasU,
      fresnelPower: fresnelPowerU,
      fresnelScale: fresnelScaleU,
    }

    // ─── Height storage buffers (ping-pong) ────────────────────────────────────
    const totalCells = resX * resZ
    const heightStorageA = instancedArray(new Float32Array(totalCells)).setName('HeightA')
    const heightStorageB = instancedArray(new Float32Array(totalCells)).setName('HeightB')
    const prevHeightStorage = instancedArray(new Float32Array(totalCells)).setName('PrevHeight')
    this.heightStorageA = heightStorageA
    this.heightStorageB = heightStorageB

    // ─── Neighbor indices helper ───────────────────────────────────────────────
    const getNeighborIndices = (index) => {
      const x = int(index.mod(resX))
      const yy = int(index.div(resX))
      const leftX = max(0, x.sub(1))
      const rightX = min(x.add(1), int(resX - 1))
      const bottomY = max(0, yy.sub(1))
      const topY = min(yy.add(1), int(resZ - 1))
      return {
        west: yy.mul(resX).add(leftX),
        east: yy.mul(resX).add(rightX),
        south: bottomY.mul(resX).add(x),
        north: topY.mul(resX).add(x),
      }
    }

    // ─── Compute height shader (ping-pong) ─────────────────────────────────────
    const createComputeHeight = (readBuffer, writeBuffer) =>
      Fn(() => {
        const h = readBuffer.element(instanceIndex).toVar()
        const prevHeight = prevHeightStorage.element(instanceIndex).toVar()

        const { north, south, east, west } = getNeighborIndices(instanceIndex)
        const neighborHeight = readBuffer
          .element(north)
          .add(readBuffer.element(south))
          .add(readBuffer.element(east))
          .add(readBuffer.element(west))
          .toVar()
        neighborHeight.mulAssign(0.5) // MUST stay 0.5 for wave equation stability
        neighborHeight.subAssign(prevHeight)

        // Scale viscosity by cell size so damping is per world-unit, independent of resolution & pool size
        const cellSize = float(Math.sqrt((sizeX / resX) * (sizeZ / resZ)))
        const newHeight = neighborHeight.mul(viscosityU.pow(cellSize)).toVar()

        // Cell world-local position
        const cx = float(globalId.x)
          .mul(1 / resX)
          .sub(0.5)
          .mul(sizeX)
        const cz = float(globalId.y)
          .mul(1 / resZ)
          .sub(0.5)
          .mul(sizeZ)
        const cellPos = vec2(cx, cz)

        // Mouse influence (depression)
        const mousePhase = clamp(length(cellPos.sub(mousePosU)).mul(Math.PI).div(mouseSizeU), 0.0, Math.PI)
        newHeight.subAssign(cos(mousePhase).add(1.0).mul(mouseDeepU).mul(length(mouseSpeedU)))

        // Sphere collider
        const xzDistSq = cellPos.sub(colliderPosU).dot(cellPos.sub(colliderPosU))
        const colliderDist = xzDistSq.add(colliderYU.mul(colliderYU)).sqrt()
        const colliderInfluence = clamp(float(1).sub(colliderDist.div(colliderRadiusU)), 0.0, 1.0)
        newHeight.subAssign(colliderInfluence.mul(colliderStrengthU))

        // Perlin noise baseline disturbance
        // Scale noise contribution so amplitude is independent of grid resolution
        const noiseCoord = vec3(cx.mul(noiseFrequencyU), cz.mul(noiseFrequencyU), time.mul(noiseSpeedU))
        const resScale = float((128 * 128) / (resX * resZ))
        newHeight.addAssign(
          mx_fractal_noise_float(noiseCoord, 2, 2.0, 0.5).mul(noiseAmplitudeU).mul(0.01).mul(resScale),
        )

        // Velocity damping
        const waveVelocity = newHeight.sub(h)
        newHeight.subAssign(waveVelocity.mul(dampingU))

        // Time scale interpolation
        const finalHeight = mix(h, newHeight, speedU)

        prevHeightStorage.element(instanceIndex).assign(h)
        writeBuffer.element(instanceIndex).assign(finalHeight)
      })().compute(totalCells, [WORKGROUP_SIZE, WORKGROUP_SIZE])

    this.#computeHeightAtoB = createComputeHeight(heightStorageA, heightStorageB)
    this.#computeHeightBtoA = createComputeHeight(heightStorageB, heightStorageA)

    // ─── Height/normal access ──────────────────────────────────────────────────
    const getCurrentHeight = (index) => select(readFromA, heightStorageA.element(index), heightStorageB.element(index))

    const getCurrentNormals = (index) => {
      const { north, south, east, west } = getNeighborIndices(index)
      return {
        normalX: getCurrentHeight(west)
          .sub(getCurrentHeight(east))
          .mul(resX / sizeX),
        normalZ: getCurrentHeight(south)
          .sub(getCurrentHeight(north))
          .mul(resZ / sizeZ),
      }
    }

    // ─── Water mesh ────────────────────────────────────────────────────────────
    const waterGeo = new THREE.PlaneGeometry(sizeX, sizeZ, resX - 1, resZ - 1)
    const waterMat = new THREE.MeshStandardNodeMaterial({
      color,
      metalness,
      roughness,
      transparent: true,
    })

    waterMat.positionNode = Fn(() => vec3(positionLocal.x, positionLocal.y, getCurrentHeight(vertexIndex)))()

    waterMat.normalNode = Fn(() => {
      const { normalX, normalZ } = getCurrentNormals(vertexIndex)
      return transformNormalToView(vec3(normalX, normalZ.negate(), 1.0)).toVertexStage()
    })()

    // Fresnel-based opacity: transparent looking down, opaque at grazing angles
    waterMat.opacityNode = Fn(() => {
      const { normalX, normalZ } = getCurrentNormals(vertexIndex)
      // World normal: local (normalX, -normalZ, 1) rotated by -PI/2 around X → (normalX, 1, normalZ)
      const worldNorm = normalize(vec3(normalX, float(1.0), normalZ))
      const eye = normalize(positionWorld.sub(cameraPosition))
      const fresnel = clamp(
        fresnelBiasU.add(fresnelScaleU.mul(float(1).add(eye.dot(worldNorm)).pow(fresnelPowerU))),
        0,
        1,
      )
      return fresnel.toVertexStage()
    })()

    const waterMesh = new THREE.Mesh(waterGeo, waterMat)
    waterMesh.rotation.x = -Math.PI / 2
    waterMesh.position.copy(center)
    waterMesh.matrixAutoUpdate = false
    waterMesh.updateMatrix()
    waterMesh.receiveShadow = true
    scene.add(waterMesh)

    this.mesh = waterMesh
    this.material = waterMat

    // ─── Raycast plane ─────────────────────────────────────────────────────────
    const raycastMesh = new THREE.Mesh(
      new THREE.PlaneGeometry(sizeX, sizeZ),
      new THREE.MeshBasicMaterial({ visible: false }),
    )
    raycastMesh.rotation.x = -Math.PI / 2
    raycastMesh.position.copy(center)
    raycastMesh.matrixAutoUpdate = false
    raycastMesh.updateMatrix()
    scene.add(raycastMesh)

    this.#raycastMesh = raycastMesh
    this.#raycaster = new THREE.Raycaster()
    this.#pingPong = 0
  }

  update(mouseNDC, camera, colliderPosition, colliderRadius) {
    // Raycast mouse against water plane
    this.#raycaster.setFromCamera(mouseNDC, camera)
    const intersects = this.#raycaster.intersectObject(this.#raycastMesh)
    if (intersects.length > 0) {
      const pt = intersects[0].point
      const localX = pt.x - this.center.x
      const localZ = pt.z - this.center.z
      this.#mouseSpeedU.value.set(localX - this.#mousePosU.value.x, localZ - this.#mousePosU.value.y)
      this.#mousePosU.value.set(localX, localZ)
    } else {
      this.#mouseSpeedU.value.set(0, 0)
    }

    // Update collider uniform
    this.#colliderPosU.value.set(colliderPosition.x - this.center.x, colliderPosition.z - this.center.z)
    this.#colliderYU.value = colliderPosition.y - this.center.y
    this.#colliderRadiusU.value = colliderRadius

    // Ping-pong compute
    const dispatch = [this.#dispatchX, this.#dispatchZ, 1]
    if (this.#pingPong === 0) {
      this.#renderer.compute(this.#computeHeightAtoB, dispatch)
      this.readFromA.value = 0
    } else {
      this.#renderer.compute(this.#computeHeightBtoA, dispatch)
      this.readFromA.value = 1
    }
    this.#pingPong = 1 - this.#pingPong
  }
}



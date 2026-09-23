import * as THREE from 'three/webgpu'
import {
  float,
  vec3,
  vec4,
  mix,
  Fn,
  vertexIndex,
  int,
  max,
  length,
  clamp,
  select,
  normalize,
  cross,
  refract,
  uniform,
  property,
  output,
} from 'three/tsl'
import { PhysicalLightingModel } from 'three/webgpu'

// Property node to pass shadow factor from lighting model to outputNode
const causticShadow = property('float', 'causticShadow')

class CausticLightingModel extends PhysicalLightingModel {
  direct(args, builder) {
    super.direct(args, builder)
    if (args.lightNode.shadowNode) {
      causticShadow.assign(args.lightNode.shadowNode)
    }
  }
}

export class WaterCaustics {
  constructor(
    scene,
    waterPlane,
    {
      lightDir,
      floorY,
      ior = 1.33,
      causticColor,
      causticStrength,
      baseColor,
      shadowInfluence = 1.0,
      heightMultiplier = 1.0,
    } = {},
  ) {
    const { resX, resZ, sizeX, sizeZ, center, heightStorageA, heightStorageB, readFromA } = waterPlane
    const cx = center.x
    const cy = center.y
    const cz = center.z

    const eta = float(1.0 / ior)
    const floorYF = float(floorY)
    const causticsFactor = float(0.15)
    const shadowInfluenceU = uniform(shadowInfluence)
    const heightMul = heightMultiplier ?? float(1)

    // ── Helpers ──────────────────────────────────────────────────────────────
    const getHeight = (idx) => {
      const raw = select(readFromA, heightStorageA.element(idx), heightStorageB.element(idx))
      const clampFactor = 0.2
      return clamp(raw.mul(heightMul), float(-clampFactor), float(clampFactor))
    }

    const safeIdx = (ix, iz) =>
      clamp(iz, 0, resZ - 1)
        .mul(resX)
        .add(clamp(ix, 0, resX - 1))

    const waterWorldPos = (ix, iz) => {
      const h = getHeight(safeIdx(ix, iz))
      return vec3(
        float(ix)
          .div(resX - 1)
          .sub(0.5)
          .mul(sizeX)
          .add(cx),
        float(cy).add(h),
        float(iz)
          .div(resZ - 1)
          .sub(0.5)
          .mul(sizeZ)
          .add(cz),
      )
    }

    const waterNormal = (ix, iz) => {
      const hL = getHeight(safeIdx(ix.sub(1), iz))
      const hR = getHeight(safeIdx(ix.add(1), iz))
      const hD = getHeight(safeIdx(ix, iz.sub(1)))
      const hU = getHeight(safeIdx(ix, iz.add(1)))
      return normalize(vec3(hL.sub(hR).mul((0.5 * resX) / sizeX), float(1.0), hD.sub(hU).mul((0.5 * resZ) / sizeZ)))
    }

    const projectToFloor = (wPos, wNorm) => {
      const rd = refract(lightDir, wNorm, eta)
      const t = floorYF.sub(wPos.y).div(rd.y)
      return vec3(wPos.x.add(rd.x.mul(t)), floorYF, wPos.z.add(rd.z.mul(t)))
    }

    // ── Geometry & Material ─────────────────────────────────────────────────
    const geo = new THREE.PlaneGeometry(sizeX, sizeZ, resX - 1, resZ - 1)
    const mat = new THREE.MeshStandardNodeMaterial({
      roughness: 0.9,
      metalness: 0,
    })

    // Override lighting model to capture shadow factor
    mat.setupLightingModel = () => new CausticLightingModel()

    // ── Vertex position: project refracted ray to floor ─────────────────────
    mat.positionNode = Fn(() => {
      const ix = int(vertexIndex.mod(resX))
      const iz = int(vertexIndex.div(resX))
      const wPos = waterWorldPos(ix, iz)
      const wNorm = waterNormal(ix, iz)
      const fPos = projectToFloor(wPos, wNorm)

      // Edge fade factor (same as color fade) to pin edges to grid positions
      const u = float(ix).div(resX - 1)
      const v = float(iz).div(resZ - 1)
      const fadeX = u.mul(float(1).sub(u)).mul(4).clamp(0, 1)
      const fadeZ = v.mul(float(1).sub(v)).mul(4).clamp(0, 1)
      const fade = fadeX.mul(fadeZ)

      // Original grid position on the floor (no refraction)
      const origLocal = vec3(
        float(ix)
          .div(resX - 1)
          .sub(0.5)
          .mul(sizeX),
        float(iz)
          .div(resZ - 1)
          .sub(0.5)
          .mul(sizeZ)
          .negate(),
        float(0),
      )
      // Refracted position in local space
      const refractedLocal = vec3(fPos.x.sub(cx), float(cz).sub(fPos.z), float(0))

      return mix(origLocal, refractedLocal, fade)
    })()

    // ── Caustic intensity (vertex stage varying) ─────────────────────────────
    const col = causticColor ?? vec3(1, 1, 1)
    const str = causticStrength ?? float(1)
    const base = baseColor ?? vec3(0, 0, 0)

    // Compute caustic as a varying (vertex → fragment interpolation)
    const causticVarying = Fn(() => {
      const ix = int(vertexIndex.mod(resX))
      const iz = int(vertexIndex.div(resX))

      // Self
      const wPos = waterWorldPos(ix, iz)
      const wNorm = waterNormal(ix, iz)
      const fPos = projectToFloor(wPos, wNorm)

      // East neighbor
      const wPosE = waterWorldPos(ix.add(1), iz)
      const wNormE = waterNormal(ix.add(1), iz)
      const fPosE = projectToFloor(wPosE, wNormE)

      // North neighbor
      const wPosN = waterWorldPos(ix, iz.add(1))
      const wNormN = waterNormal(ix, iz.add(1))
      const fPosN = projectToFloor(wPosN, wNormN)

      // Area ratio: oldArea (water surface triangle) / newArea (floor triangle)
      const oldArea = length(cross(wPosE.sub(wPos), wPosN.sub(wPos)))
      const newArea = length(cross(fPosE.sub(fPos), fPosN.sub(fPos)))
      const intensity = causticsFactor.mul(oldArea.div(max(newArea, float(0.00001))))

      // Edge fade
      const u = float(ix).div(resX - 1)
      const v = float(iz).div(resZ - 1)
      const fadeX = u.mul(float(1).sub(u)).mul(4).clamp(0, 1)
      const fadeZ = v.mul(float(1).sub(v)).mul(4).clamp(0, 1)

      return col.mul(intensity.mul(fadeX).mul(fadeZ)).mul(str).toVertexStage()
    })()

    // Base color only (no caustics in albedo)
    mat.colorNode = base

    // Add caustics in outputNode, masked by shadow factor
    // shadow=1 → lit (caustics show), shadow=0 → shadowed (no caustics)
    mat.outputNode = Fn(() => {
      const litColor = output
      const shadowMask = mix(float(1), causticShadow, shadowInfluenceU)
      return vec4(litColor.rgb.add(causticVarying.mul(shadowMask)), litColor.a)
    })()

    const mesh = new THREE.Mesh(geo, mat)
    mesh.rotation.x = -Math.PI / 2
    mesh.position.set(cx, floorY, cz)
    mesh.matrixAutoUpdate = false
    mesh.updateMatrix()
    mesh.receiveShadow = true
    mesh.frustumCulled = false
    scene.add(mesh)

    this.mesh = mesh
    this.material = mat
    this.uniforms = { shadowInfluence: shadowInfluenceU }
  }
}



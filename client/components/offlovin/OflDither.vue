<template>
  <canvas ref="lienzo" class="ofl-dither" aria-hidden="true" />
</template>

<script setup>
// OFFLOVIN: fondo de semitono y dithering animado (WebGL) para el panel de marca del login y el registro.
// Respeta «reducir movimiento» (un solo fotograma) y deja de pintar cuando la pestaña no se ve.
const lienzo = ref(null)
let gl = null
let rafId = 0
let observador = null

const VERT = 'attribute vec2 p; void main(){ gl_Position = vec4(p, 0.0, 1.0); }'
const FRAG = `
precision highp float;
uniform vec2 uRes;
uniform float uTime;
uniform float uDpr;
uniform float uCompact;

float hash(vec2 p){ return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
float noise(vec2 p){
  vec2 i = floor(p), f = fract(p);
  vec2 u = f * f * (3.0 - 2.0 * f);
  return mix(mix(hash(i), hash(i + vec2(1.0, 0.0)), u.x),
             mix(hash(i + vec2(0.0, 1.0)), hash(i + vec2(1.0, 1.0)), u.x), u.y);
}
float fbm(vec2 p){
  float v = 0.0, a = 0.5;
  for (int i = 0; i < 5; i++) { v += a * noise(p); p = p * 2.03 + 17.0; a *= 0.5; }
  return v;
}
float bayer2(vec2 a){ a = floor(a); return fract(a.x / 2.0 + a.y * a.y * 0.75); }
float bayer4(vec2 a){ return bayer2(0.5 * a) * 0.25 + bayer2(a); }
float bayer8(vec2 a){ return bayer4(0.5 * a) * 0.25 + bayer2(a); }

vec3 ramp(float t){
  vec3 carbon = vec3(0.059, 0.067, 0.075);
  vec3 violet = vec3(0.545, 0.541, 0.945);
  vec3 pink   = vec3(1.000, 0.161, 0.471);
  vec3 orchid = vec3(1.000, 0.561, 0.965);
  vec3 cream  = vec3(0.953, 0.937, 0.906);
  t = clamp(t, 0.0, 1.0);
  if (t < 0.28) return mix(carbon, violet, t / 0.28);
  if (t < 0.55) return mix(violet, pink, (t - 0.28) / 0.27);
  if (t < 0.80) return mix(pink, orchid, (t - 0.55) / 0.25);
  return mix(orchid, cream, (t - 0.80) / 0.20);
}

// Campo que fluye: el corazón del movimiento
float field(vec2 px){
  vec2 uv = px / uRes.y;
  float t = uTime * 0.05;
  vec2 q = vec2(fbm(uv * 1.5 + vec2(0.0, t)), fbm(uv * 1.5 + vec2(5.2, -t)));
  float f = fbm(uv * 1.3 + 1.8 * q + vec2(t * 0.7, -t * 0.3));
  // más luz abajo, oscuro arriba para que el logo respire
  float y = px.y / uRes.y;
  f = f * 1.25 - 0.18 + (0.35 - y) * 0.55;
  return clamp(f, 0.0, 1.0);
}

mat2 rot(float a){ float c = cos(a), s = sin(a); return mat2(c, -s, s, c); }

float dith(float v, float th, float levels){ return floor(v * levels + th) / levels; }
vec3 dith3(vec3 c, float th, float levels){ return floor(c * levels + th) / levels; }
float box(vec2 n, vec4 r){ return step(r.x, n.x) * step(n.x, r.z) * step(r.y, n.y) * step(n.y, r.w); }

void main(){
  vec2 px = gl_FragCoord.xy;
  vec2 n = px / uRes;
  float T = uTime;
  vec3 carbon = vec3(0.059, 0.067, 0.075);
  vec3 col = carbon;

  vec2 dp = floor(px / (2.0 * uDpr));
  float th = bayer8(dp);

  // Semitono: solo donde el campo supera el umbral, concentrado abajo a la derecha
  float cell = 9.0 * uDpr;
  mat2 R = rot(0.38);
  vec2 g = R * (px / cell);
  vec2 id = floor(g);
  vec2 fr = fract(g) - 0.5;
  vec2 c = rot(-0.38) * (id + 0.5) * cell;
  vec2 cn = c / uRes;
  float f = field(c) + (cn.x - 0.45) * 0.55 + (0.5 - cn.y) * 0.45;
  float k = smoothstep(0.38 - uCompact * 0.08, 1.05, f);
  float r = sqrt(k) * 0.62;
  float aa = 1.0 / cell;
  float dotMask = (1.0 - smoothstep(r - aa, r + aa, length(fr))) * step(0.001, k);
  vec3 dotCol = ramp(0.30 + k * 0.62 + (cn.x - 0.5) * 0.15);
  col = mix(col, dotCol, dotMask);

  float drift = 0.05 * sin(T * 0.21);
  float drift2 = 0.04 * sin(T * 0.17 + 1.3);

  // Bloque crema con grano que pasa a rosa
  float bA = box(n, vec4(0.0, 0.43, 0.52, 0.53));
  float vA = smoothstep(0.05, 0.60, n.x / 0.52 + drift);
  vec3 cA = mix(vec3(0.953, 0.937, 0.906), vec3(1.0, 0.561, 0.965), vA);
  cA = dith3(cA - (hash(dp) - 0.5) * 0.10, th, 6.0);

  // Banda ancha crema a orquídea, lisa con dithering fino
  float bB = box(n, vec4(0.0, 0.31, 0.72, 0.43));
  float vB = clamp(n.x / 0.72 + drift2, 0.0, 1.0);
  vec3 cB = mix(vec3(0.953, 0.937, 0.906), vec3(1.0, 0.561, 0.965), smoothstep(0.08, 0.55, vB));
  cB = mix(cB, vec3(0.988, 0.47, 0.95), smoothstep(0.55, 1.0, vB));
  cB = dith3(cB, th, 10.0);

  // Barras verticales rosas
  float bS = box(n, vec4(0.72, 0.0, 0.84, 0.53));
  float stripe = 0.5 + 0.5 * sin((n.x - 0.72) * 260.0);
  float vS = 0.62 + 0.18 * sin(n.y * 6.0 - T * 0.35) + stripe * 0.08;
  vec3 cS = dith3(ramp(vS), th, 6.0);

  // Bloque bajo: violeta, rosa y crema en semitono fino
  float bC = box(n, vec4(0.0, 0.0, 0.56, 0.17));
  float vC = clamp(n.x / 0.56 - drift, 0.0, 1.0);
  vec3 cC = vC < 0.5 ? mix(vec3(0.545, 0.541, 0.945), vec3(1.0, 0.161, 0.471), vC * 2.0)
                     : mix(vec3(1.0, 0.161, 0.471), vec3(0.953, 0.937, 0.906), (vC - 0.5) * 2.0);
  float fine = step(0.5, fract(px.x / (3.0 * uDpr)) ) * step(0.5, fract(px.y / (3.0 * uDpr)));
  cC = dith3(cC * (0.86 + 0.14 * fine), th, 5.0);

  // Relleno entre bloques: orquídea suave
  float bD = box(n, vec4(0.0, 0.17, 0.72, 0.31));
  vec3 cD = dith3(mix(vec3(1.0, 0.561, 0.965), vec3(1.0, 0.70, 0.98), smoothstep(0.0, 1.0, n.x / 0.72 - drift2)), th, 8.0);

  if (uCompact > 0.5) {
    // Móvil: solo el semitono y una banda baja a todo el ancho
    float bM = box(n, vec4(0.0, 0.0, 1.0, 0.16));
    vec3 cM = dith3(ramp(0.35 + clamp(n.x - drift, 0.0, 1.0) * 0.6), th, 6.0);
    col = mix(col, cM, bM);
  } else {
    col = mix(col, cD, bD);
    col = mix(col, cB, bB);
    col = mix(col, cA, bA);
    col = mix(col, cS, bS);
    col = mix(col, cC, bC);
  }

  col += (hash(px + floor(T * 12.0)) - 0.5) * 0.02;
  gl_FragColor = vec4(col, 1.0);
}
`

function compilar(tipo, codigo) {
  const s = gl.createShader(tipo)
  gl.shaderSource(s, codigo)
  gl.compileShader(s)
  if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) throw new Error(gl.getShaderInfoLog(s))
  return s
}

onMounted(() => {
  const canvas = lienzo.value
  gl = canvas?.getContext('webgl', { antialias: false, premultipliedAlpha: false })
  if (!gl) return

  const prog = gl.createProgram()
  try {
    gl.attachShader(prog, compilar(gl.VERTEX_SHADER, VERT))
    gl.attachShader(prog, compilar(gl.FRAGMENT_SHADER, FRAG))
  } catch (e) {
    console.warn('Fondo dithering desactivado:', e)
    return
  }
  gl.linkProgram(prog)
  gl.useProgram(prog)

  const buf = gl.createBuffer()
  gl.bindBuffer(gl.ARRAY_BUFFER, buf)
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 1, -1, -1, 1, 1, 1]), gl.STATIC_DRAW)
  const loc = gl.getAttribLocation(prog, 'p')
  gl.enableVertexAttribArray(loc)
  gl.vertexAttribPointer(loc, 2, gl.FLOAT, false, 0, 0)

  const uRes = gl.getUniformLocation(prog, 'uRes')
  const uTime = gl.getUniformLocation(prog, 'uTime')
  const uDpr = gl.getUniformLocation(prog, 'uDpr')
  const uCompact = gl.getUniformLocation(prog, 'uCompact')

  const quieto = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  const t0 = performance.now()
  let ultimo = 0

  const pintar = (ahora) => {
    gl.uniform1f(uTime, quieto ? 24.0 : (ahora - t0) / 1000 + 24.0)
    gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4)
  }

  const ajustar = () => {
    const d = Math.min(window.devicePixelRatio || 1, 1.5)
    const w = Math.max(1, Math.round(canvas.clientWidth * d))
    const h = Math.max(1, Math.round(canvas.clientHeight * d))
    if (canvas.width !== w || canvas.height !== h) {
      canvas.width = w
      canvas.height = h
    }
    gl.viewport(0, 0, w, h)
    gl.uniform2f(uRes, w, h)
    gl.uniform1f(uDpr, d)
    gl.uniform1f(uCompact, canvas.clientWidth > canvas.clientHeight * 1.3 ? 1.0 : 0.0)
    pintar(performance.now())
  }

  observador = new ResizeObserver(ajustar)
  observador.observe(canvas)
  ajustar()

  if (quieto) return
  const bucle = (ahora) => {
    // 30 fotogramas por segundo bastan para un movimiento tan lento
    if (ahora - ultimo > 33) {
      ultimo = ahora
      pintar(ahora)
    }
    rafId = requestAnimationFrame(bucle)
  }
  rafId = requestAnimationFrame(bucle)
})

onBeforeUnmount(() => {
  cancelAnimationFrame(rafId)
  observador?.disconnect()
  gl?.getExtension('WEBGL_lose_context')?.loseContext()
})
</script>

<style>
.ofl-dither {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  display: block;
}
</style>

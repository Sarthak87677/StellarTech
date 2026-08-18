/**
 * StellarTech — solar system background.
 * Loads after three.min.js from the CDN, so THREE is a global here.
 * Real radii, semi-major axes, axial tilts and sidereal periods,
 * compressed for display.
 */
const reduceSpace = matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ============================================================
   SOLAR SYSTEM — real astronomical data, compressed for display.
   Radii, semi-major axes and axial tilts are actual measured values.
   Relative orbital speeds follow real sidereal periods, so Neptune
   really does crawl while Mercury races. Distances and sizes are
   compressed logarithmically or the inner planets would be invisible.
   The club logo is DOM/raster and never enters this scene.
   ============================================================ */
(function(){
  const canvas = document.getElementById('space');
  const fb = document.getElementById('spacefallback');
  const weak = reduceSpace || innerWidth < 620
    || (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2)
    || typeof THREE === 'undefined'
    ;
  if (weak) { canvas.style.display='none'; fb.classList.add('on'); return; }

  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:false, powerPreference:'high-performance'});
  } catch(e){ canvas.style.display='none'; fb.classList.add('on'); return; }

  const DPR = Math.min(devicePixelRatio || 1, 1.6);
  renderer.setPixelRatio(DPR);
  renderer.setSize(innerWidth, innerHeight, false);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(52, innerWidth/innerHeight, 0.5, 4000);

  /* ---------- texture helpers ---------- */
  function bandTex(base, bands, rough, w, h){
    w = w || 256; h = h || 128;
    const c = document.createElement('canvas'); c.width=w; c.height=h;
    const x = c.getContext('2d');
    x.fillStyle = base; x.fillRect(0,0,w,h);
    for (let i=0;i<bands;i++){
      const y = Math.random()*h, hh = 2 + Math.random()*(h/11);
      x.globalAlpha = .07 + Math.random()*.21;
      x.fillStyle = Math.random()>.5 ? '#ffffff' : '#000000';
      x.fillRect(0, y, w, hh);
    }
    x.globalAlpha = rough;
    for (let i=0;i<600;i++){
      x.fillStyle = Math.random()>.5 ? '#ffffff' : '#000000';
      const s = Math.random()*4+1;
      x.fillRect(Math.random()*w, Math.random()*h, s, s);
    }
    const t = new THREE.CanvasTexture(c); t.wrapS = THREE.RepeatWrapping; return t;
  }
  function glow(color, size, opacity){
    const c = document.createElement('canvas'); c.width=c.height=256;
    const x = c.getContext('2d');
    const g = x.createRadialGradient(128,128,0,128,128,128);
    g.addColorStop(0,color+'ff'); g.addColorStop(.16,color+'bb');
    g.addColorStop(.42,color+'33'); g.addColorStop(1,color+'00');
    x.fillStyle=g; x.fillRect(0,0,256,256);
    const s = new THREE.Sprite(new THREE.SpriteMaterial({map:new THREE.CanvasTexture(c),
      blending:THREE.AdditiveBlending, transparent:true, depthWrite:false, opacity}));
    s.scale.set(size,size,1); return s;
  }
  function sparkle(size){
    const c = document.createElement('canvas'); c.width=c.height=128;
    const x = c.getContext('2d');
    const g = x.createRadialGradient(64,64,0,64,64,20);
    g.addColorStop(0,'#ffffffff'); g.addColorStop(1,'#ffffff00');
    x.fillStyle=g; x.beginPath(); x.arc(64,64,20,0,7); x.fill();
    x.strokeStyle='#ffffff'; x.lineCap='round';
    [[64,4,64,124],[4,64,124,64]].forEach(([a,b,cc,d],i)=>{
      x.globalAlpha = i ? .55 : .8; x.lineWidth = i ? 1.6 : 2.2;
      x.beginPath(); x.moveTo(a,b); x.lineTo(cc,d); x.stroke();
    });
    const s = new THREE.Sprite(new THREE.SpriteMaterial({map:new THREE.CanvasTexture(c),
      blending:THREE.AdditiveBlending, transparent:true, depthWrite:false, opacity:.9}));
    s.scale.set(size,size,1); return s;
  }

  /* ---------- nebula backdrop (matches the reference's teal/violet field) ---------- */
  (function nebula(){
    const W=1024,H=1024;
    const c=document.createElement('canvas'); c.width=W; c.height=H;
    const x=c.getContext('2d');
    x.fillStyle='#02040A'; x.fillRect(0,0,W,H);
    const clouds=[
      ['#0E5C4A',.34,700,560,420],['#12796A',.22,760,470,300],
      ['#4B2C8A',.30,300,690,430],['#6A3AA8',.18,240,760,260],
      ['#123C7A',.24,520,240,400],['#2E6FA8',.14,600,180,240]
    ];
    clouds.forEach(([col,al,cx,cy,r])=>{
      const g=x.createRadialGradient(cx,cy,0,cx,cy,r);
      g.addColorStop(0,col); g.addColorStop(1,'#02040A00');
      x.globalAlpha=al; x.fillStyle=g; x.beginPath(); x.arc(cx,cy,r,0,7); x.fill();
    });
    x.globalAlpha=1;
    for(let i=0;i<2600;i++){
      const b=Math.random();
      x.fillStyle='rgba(255,255,255,'+(0.12+b*0.75)+')';
      const s=Math.random()<.94?1:2;
      x.fillRect(Math.random()*W,Math.random()*H,s,s);
    }
    const t=new THREE.CanvasTexture(c);
    const plane=new THREE.Mesh(new THREE.PlaneGeometry(2600,2600),
      new THREE.MeshBasicMaterial({map:t, depthWrite:false}));
    plane.position.set(0,0,-950);
    scene.add(plane);
  })();

  // Foreground star sparkles, like the reference
  for (let i=0;i<22;i++){
    const s = sparkle(3 + Math.random()*7);
    s.position.set((Math.random()-.5)*520, (Math.random()-.5)*430, -160 - Math.random()*380);
    scene.add(s);
  }

  /* ---------- SUN ---------- */
  const SUN_R = 22;
  const sun = new THREE.Mesh(new THREE.SphereGeometry(SUN_R,64,48),
    new THREE.MeshBasicMaterial({map: bandTex('#FFC45E', 34, .18, 512, 256)}));
  scene.add(sun);
  const h1 = glow('#FFB347',  96, .95);
  const h2 = glow('#FF8A3D', 190, .40);
  const h3 = glow('#FFD98A', 330, .14);
  scene.add(h1,h2,h3);
  scene.add(new THREE.AmbientLight(0x7286ad, 0.34));
  scene.add(new THREE.PointLight(0xfff2d8, 2.6, 3000, 1.2));

  /* ---------- REAL PLANETARY DATA ----------
     name, equatorial radius (km), semi-major axis (AU),
     sidereal period (yr), axial tilt (deg), colour, band count
  ------------------------------------------- */
  const DATA = [
    ['Mercury',  2439.7,  0.387,   0.2408,   0.03, '#9b8f86', 12, .32],
    ['Venus',    6051.8,  0.723,   0.6152, 177.36, '#d9b982', 18, .16],
    ['Earth',    6371.0,  1.000,   1.0000,  23.44, '#2f6db5', 22, .34],
    ['Mars',     3389.5,  1.524,   1.8808,  25.19, '#c1572f', 15, .36],
    ['Jupiter', 69911.0,  5.204,  11.8620,   3.13, '#c9a37a', 40, .12],
    ['Saturn',  58232.0,  9.583,  29.4570,  26.73, '#dcc793', 34, .12],
    ['Uranus',  25362.0, 19.191,  84.0110,  97.77, '#8fd3dd', 16, .10],
    ['Neptune', 24622.0, 30.070, 164.7900,  28.32, '#3f63c9', 16, .12]
  ];

  // Compression: real values preserved in rank and ratio, squashed to fit frame.
  const rDisp = km  => 1.15 * Math.pow(km / 6371, 0.46) * 3.0;   // Earth ≈ 3.45 units
  const aDisp = au  => 42 * Math.pow(au, 0.52);                  // Earth ≈ 42 units
  const wDisp = yr  => 0.62 / Math.pow(yr, 0.62);                // Keplerian-ordered

  const system = new THREE.Group();
  scene.add(system);

  const planets = DATA.map(([name, km, au, yr, tilt, col, bands, rough]) => {
    const R = rDisp(km), A = aDisp(au);
    const pivot = new THREE.Object3D();
    pivot.rotation.y = Math.random() * Math.PI * 2;
    system.add(pivot);

    const mesh = new THREE.Mesh(new THREE.SphereGeometry(R, 44, 32),
      new THREE.MeshStandardMaterial({map: bandTex(col, bands, rough), roughness:.94, metalness:.02}));
    mesh.position.x = A;
    mesh.rotation.z = tilt * Math.PI / 180;   // real axial tilt
    pivot.add(mesh);

    if (name === 'Earth')  mesh.add(glow('#5AA9FF', R*5.4, .38));
    if (name === 'Venus')  mesh.add(glow('#FFD9A0', R*4.6, .26));
    if (name === 'Saturn') {
      // Real ring span: roughly 1.24 to 2.27 planetary radii (C ring to A ring)
      const rg = new THREE.Mesh(new THREE.RingGeometry(R*1.24, R*2.27, 128),
        new THREE.MeshBasicMaterial({color:0xD6C296, transparent:true, opacity:.5,
          side:THREE.DoubleSide, depthWrite:false}));
      rg.rotation.x = Math.PI/2;
      mesh.add(rg);
    }
    if (name === 'Uranus') {
      const rg = new THREE.Mesh(new THREE.RingGeometry(R*1.6, R*2.0, 96),
        new THREE.MeshBasicMaterial({color:0x9FD8E0, transparent:true, opacity:.24,
          side:THREE.DoubleSide, depthWrite:false}));
      rg.rotation.x = Math.PI/2;
      mesh.add(rg);
    }

    // Orbit path
    const ring = new THREE.Mesh(new THREE.RingGeometry(A-0.10, A+0.10, 260),
      new THREE.MeshBasicMaterial({color:0xC9D8EE, transparent:true, opacity:.13,
        side:THREE.DoubleSide, depthWrite:false}));
    ring.rotation.x = -Math.PI/2;
    system.add(ring);

    return {pivot, mesh, w: wDisp(yr)};
  });

  /* ---------- ASTEROID BELTS ----------
     Main belt: 2.1–3.3 AU.  Kuiper belt: 30–50 AU.
  ------------------------------------- */
  function belt(auMin, auMax, count, spread, colA, colB, size){
    const p = new Float32Array(count*3), c = new Float32Array(count*3);
    const ca = new THREE.Color(colA), cb = new THREE.Color(colB);
    for (let i=0;i<count;i++){
      const a = Math.random()*Math.PI*2;
      const r = aDisp(auMin + Math.random()*(auMax-auMin));
      p[i*3]   = Math.cos(a)*r;
      p[i*3+1] = (Math.random()-.5)*spread;
      p[i*3+2] = Math.sin(a)*r;
      const m = ca.clone().lerp(cb, Math.random());
      const b = .55 + Math.random()*.45;
      c[i*3]=m.r*b; c[i*3+1]=m.g*b; c[i*3+2]=m.b*b;
    }
    const g = new THREE.BufferGeometry();
    g.setAttribute('position', new THREE.BufferAttribute(p,3));
    g.setAttribute('color', new THREE.BufferAttribute(c,3));
    const pts = new THREE.Points(g, new THREE.PointsMaterial({size, sizeAttenuation:true,
      vertexColors:true, transparent:true, opacity:.92, depthWrite:false}));
    system.add(pts); return pts;
  }
  const mainBelt   = belt(2.1, 3.3, 5200, 3.2, '#E3C489', '#8A6E4B', 1.5);
  const kuiperBelt = belt(30, 50, 4200, 6.0, '#BFD3E8', '#5C7194', 1.25);

  /* ---------- deep starfield ---------- */
  (function stars(){
    const N=2200, p=new Float32Array(N*3), c=new Float32Array(N*3);
    const tint=[[1,1,1],[.72,.84,1],[1,.9,.78],[.85,.8,1]];
    for(let i=0;i<N;i++){
      const r=520+Math.random()*760, th=Math.random()*Math.PI*2, ph=Math.acos(2*Math.random()-1);
      p[i*3]=r*Math.sin(ph)*Math.cos(th); p[i*3+1]=r*Math.cos(ph)*.8; p[i*3+2]=r*Math.sin(ph)*Math.sin(th);
      const t=tint[(Math.random()*tint.length)|0], b=.45+Math.random()*.55;
      c[i*3]=t[0]*b; c[i*3+1]=t[1]*b; c[i*3+2]=t[2]*b;
    }
    const g=new THREE.BufferGeometry();
    g.setAttribute('position',new THREE.BufferAttribute(p,3));
    g.setAttribute('color',new THREE.BufferAttribute(c,3));
    scene.add(new THREE.Points(g,new THREE.PointsMaterial({size:1.6,sizeAttenuation:true,
      vertexColors:true,transparent:true,opacity:.92,depthWrite:false})));
  })();

  /* ---------- CAMERA: low, outside the plane, sun riding high in frame.
       Reproduces the reference composition — nested orbit arcs sweeping
       upward toward a sun that sits near the top edge. ---------- */
  let px=0, py=0, cx=0, cy=0;
  addEventListener('pointermove', e => {
    px = (e.clientX/innerWidth - .5); py = (e.clientY/innerHeight - .5);
  }, {passive:true});
  addEventListener('resize', () => {
    camera.aspect = innerWidth/innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(innerWidth, innerHeight, false);
  }, {passive:true});

  let hidden=false;
  document.addEventListener('visibilitychange', () => hidden = document.hidden);

  const clock = new THREE.Clock();
  const LOOK_Y = -118;   // aim below the sun so it sits high in frame

  function frame(){
    requestAnimationFrame(frame);
    if (hidden) return;
    const t = clock.getElapsedTime();

    planets.forEach(p => { p.pivot.rotation.y += p.w * 0.0020; p.mesh.rotation.y += 0.0030; });
    sun.rotation.y      += 0.00055;
    mainBelt.rotation.y += 0.00035;
    kuiperBelt.rotation.y += 0.00009;

    const b = 1 + Math.sin(t*0.5)*0.04;
    h1.scale.set(96*b, 96*b, 1);
    h2.scale.set(190*b, 190*b, 1);

    cx += (px - cx) * 0.028;
    cy += (py - cy) * 0.028;

    const a = t * 0.014;                       // very slow drift around the system
    camera.position.set(
      Math.sin(a) * 78 + cx * 30,
      -104 + Math.sin(t*0.10) * 6 - cy * 22,   // below the ecliptic, looking up
       270 + Math.cos(a) * 34
    );
    camera.lookAt(0, LOOK_Y, 0);

    renderer.render(scene, camera);
  }
  frame();
})();

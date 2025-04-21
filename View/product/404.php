<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>404 - Không gian hư vô</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      height: 100vh;
      overflow: hidden;
      background: radial-gradient(ellipse at center, #0f0c29, #302b63, #24243e);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #cdd6f4;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      position: relative;
    }

    /* Glow text */
    h1 {
      font-size: 8rem;
      font-weight: bold;
      color: #8be9fd;
      text-shadow: 0 0 15px #8be9fd, 0 0 30px #50fa7b, 0 0 45px #bd93f9;
      animation: pulseGlow 2.5s infinite alternate;
    }

    @keyframes pulseGlow {
      from { text-shadow: 0 0 10px #50fa7b; }
      to { text-shadow: 0 0 30px #8be9fd, 0 0 60px #bd93f9; }
    }

    p {
      font-size: 1.4rem;
      margin-top: 1rem;
      color: #f8f8f2;
      opacity: 0.9;
      animation: fadeInUp 2s ease-out;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    a {
      margin-top: 2rem;
      padding: 0.75rem 1.5rem;
      background: linear-gradient(45deg, #6c63ff, #8a2be2);
      border: none;
      border-radius: 30px;
      color: white;
      font-weight: bold;
      text-decoration: none;
      font-size: 1rem;
      box-shadow: 0 0 15px rgba(138, 43, 226, 0.7);
      transition: all 0.3s ease;
      animation: fadeInUp 3s ease-out;
    }

    a:hover {
      background: linear-gradient(45deg, #7f00ff, #e100ff);
      box-shadow: 0 0 25px rgba(225, 0, 255, 0.8);
      transform: scale(1.08);
    }

    /* Soft overlay light fog */
    .fog-overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><filter id="noise"><feTurbulence type="fractalNoise" baseFrequency="0.7" numOctaves="2" result="noise"/><feBlend in="SourceGraphic" in2="noise" mode="screen"/></filter><rect width="100%" height="100%" filter="url(%23noise)" /></svg>') repeat;
      opacity: 0.03;
      pointer-events: none;
      z-index: 1;
    }

    /* Snow / mist particles */
    .snowflake {
      position: fixed;
      top: -10px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      pointer-events: none;
      z-index: 2;
      animation: fall linear infinite;
    }

    @keyframes fall {
      to {
        transform: translateY(100vh);
        opacity: 0;
      }
    }
    
    .sparkle {
      position: absolute;
      border-radius: 50%;
      background: radial-gradient(circle, #fff9c4 0%, transparent 70%);
      pointer-events: none;
      animation: sparkleAnim 1s ease-out forwards;
      z-index: 2;
    }
  </style>
</head>
<body>
  <div class="fog-overlay"></div>

  <h1>404</h1>
  <p>Bạn đã lạc vào Ultimate của Moderkaiser <b>"Không gian hư ảo"</b> </p>

  <p>Yên tâm, bạn có thể tự giải thoát bằng cách dưới đây</p>
  <a href="../../View/product/login.view.php">🚀 Trở về thực tại</a>
  

  <script>
    const snowflakes = 60;
    for (let i = 0; i < snowflakes; i++) {
      const snowflake = document.createElement("div");
      snowflake.classList.add("snowflake");
      const size = Math.random() * 4 + 1 + "px";
      snowflake.style.width = size;
      snowflake.style.height = size;
      snowflake.style.left = Math.random() * 100 + "vw";
      snowflake.style.animationDuration = (Math.random() * 8 + 4) + "s";
      snowflake.style.animationDelay = Math.random() * 5 + "s";
      document.body.appendChild(snowflake);
    }
  </script>
   <!-- Hover reactive sparkles -->
   <script>
    document.body.addEventListener("mousemove", (e) => {
      const sparkle = document.createElement("div");
      sparkle.classList.add("sparkle");
      const size = Math.random() * 8 + 4;
      sparkle.style.width = size + "px";
      sparkle.style.height = size + "px";
      sparkle.style.left = e.clientX + "px";
      sparkle.style.top = e.clientY + "px";
      document.body.appendChild(sparkle);

      setTimeout(() => sparkle.remove(), 1000);
    });
  </script>
</body>
</html>

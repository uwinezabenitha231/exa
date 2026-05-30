<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber-Ops Smart Box Control</title>
    <style>
        :root {
            --bg-color: #0a0f1d;
            --card-bg: #111827;
            --neon-green: #10b981;
            --neon-red: #f43f5e;
            --text-main: #f3f4f6;
            --border-glow: #1f2937;
        }

        body { font-family: 'Courier New', Courier, monospace; background-color: var(--bg-color); color: var(--text-main); margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 95vh; }
        
        /* LOGIN INTERFACE */
        .login-panel { background: var(--card-bg); padding: 35px; border-radius: 4px; box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); width: 100%; max-width: 350px; border: 1px solid var(--neon-green); text-align: center; }
        .login-panel h2 { color: var(--neon-green); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; font-size: 20px; }
        .field-group { margin-bottom: 20px; text-align: left; }
        .field-group label { display: block; margin-bottom: 5px; font-size: 12px; color: var(--neon-green); text-transform: uppercase; }
        .field-group input { width: 100%; padding: 10px; border: 1px solid #374151; background: #030712; color: #fff; box-sizing: border-box; font-size: 14px; border-radius: 2px; }
        .field-group input:focus { border-color: var(--neon-green); outline: none; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5); }
        .auth-btn { width: 100%; padding: 12px; background: transparent; border: 1px solid var(--neon-green); color: var(--neon-green); font-size: 14px; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: 0.3s; }
        .auth-btn:hover { background: var(--neon-green); color: #000; box-shadow: 0 0 15px var(--neon-green); }
        .fail-alert { color: var(--neon-red); font-size: 12px; margin-top: 15px; display: none; text-transform: uppercase; }

        /* CYBERPUNK DASHBOARD (Hides initially) */
        .cyber-grid { width: 100%; max-width: 1100px; display: none; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; background: var(--card-bg); border-radius: 4px; margin-bottom: 20px; border-left: 5px solid var(--neon-green); box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .top-nav h1 { margin: 0; font-size: 22px; letter-spacing: 1px; color: var(--neon-green); }
        .exit-btn { padding: 8px 16px; background: transparent; border: 1px solid var(--neon-red); color: var(--neon-red); cursor: pointer; text-transform: uppercase; font-size: 12px; }
        .exit-btn:hover { background: var(--neon-red); color: #fff; box-shadow: 0 0 10px var(--neon-red); }
        
        .layout-main { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 20px; }
        .block-card { background: var(--card-bg); padding: 20px; border-radius: 4px; border: 1px solid var(--border-glow); position: relative; }
        .block-card h3 { margin-top: 0; font-size: 14px; text-transform: uppercase; color: #9ca3af; border-bottom: 1px solid #1f2937; padding-bottom: 10px; letter-spacing: 1px; }
        
        /* STREAM DISPLAY */
        .video-box { width: 100%; height: 280px; background: #000; border-radius: 2px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #1f2937; }
        
        /* SECURITY STATUS BLOCKS */
        .indicator-box { display: flex; align-items: center; justify-content: center; padding: 20px; font-size: 24px; font-weight: bold; border-radius: 2px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px; }
        .safe { background: rgba(16, 185, 129, 0.1); border: 1px solid var(--neon-green); color: var(--neon-green); }
        
        .stat-line { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #1f2937; font-size: 14px; }
        .stat-line span:last-child { color: var(--neon-green); font-weight: bold; }

        /* LIVE LOGS TABLE */
        .logs-section { grid-column: span 2; margin-top: 10px; }
        .log-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        .log-table th { text-align: left; padding: 10px; background: #1f2937; color: #9ca3af; text-transform: uppercase; }
        .log-table td { padding: 10px; border-bottom: 1px solid #1f2937; color: #d1d5db; }
    </style>
</head>
<body>

    <!-- 1. SYSTEM SECURITY ACCESS (LOGIN) -->
    <div class="login-panel" id="loginBox">
        <h2>CORE_SYS ACCESS</h2>
        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="field-group">
                <label>OPERATOR_ID</label>
                <input type="text" id="username" required placeholder="SYS_USER">
            </div>
            <div class="field-group">
                <label>ACCESS_PASS</label>
                <input type="password" id="password" required placeholder="******">
            </div>
            <button type="submit" class="auth-btn">INITIALIZE</button>
            <div class="fail-alert" id="errorMsg">!! ACCESS DENIED: INVALID KEY !!</div>
        </form>
    </div>

    <!-- 2. CYBER OPERATIONAL DASHBOARD -->
    <div class="cyber-grid" id="dashboardBox">
        <div class="top-nav">
            <div>
                <h1>⚡ SMART LOGISTICS COMMAND CENTER</h1>
                <p style="margin: 3px 0 0 0; color: #6b7280; font-size: 11px;">Kigali Network Node: Active</p>
            </div>
            <button class="exit-btn" onclick="handleLogout()">TERMINATE</button>
        </div>

        <div class="layout-main">
            <!-- LEFT PANEL: TELEMETRY FEED -->
            <div class="block-card">
                <h3>[VIDEO_FEED_01] - ESP32-CAM CONTROLLER</h3>
                <div class="video-box">
                    <img src="http://192.168.1" alt="WAITING FOR GPRS/WIFI SIGNAL..." style="width:100%; height:100%; object-fit: cover;">
                </div>
            </div>

            <!-- RIGHT PANEL: SYSTEM TELEMETRY -->
            <div class="block-card">
                <h3>[TELEMETRY_DATA]</h3>
                <div class="indicator-box safe" id="boxStatus">// SECURED //</div>
                
                <div class="stat-line"><span>ULTRASONIC_RANGE</span><span id="distance">12 CM</span></div>
                <div class="stat-line"><span>NETWORK_PROVIDER</span><span>MTN-RWANDA</span></div>
                <div class="stat-line"><span>GSM_SIGNAL</span><span>98% [EXCELLENT]</span></div>
                <div class="stat-line"><span>SYSTEM_ENCRYPTION</span><span>AES-256</span></div>
            </div>

            <!-- BOTTOM PANEL: EVENT LOGS -->
            <div class="block-card logs-section">
                <h3>[EVENT_LOG_STREAM]</h3>
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>TIMESTAMP</th>
                            <th>EVENT TYPE</th>
                            <th>NODE ID</th>
                            <th>METRIC STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>20:14:02</td>
                            <td>SYS_INITIALIZE</td>
                            <td>KGL_NODE_01</td>
                            <td style="color: var(--neon-green);">SUCCESS</td>
                        </tr>
                        <tr>
                            <td>20:14:15</td>
                            <td>PING_GPRS</td>
                            <td>MTN_TOWER_3</td>
                            <td style="color: var(--neon-green);">CONNECTED</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT AUTHENTICATION & ROUTING LOGIC -->
    <script>
        function handleLogin(event) {
            event.preventDefault();
            
            // Kwemera amazina yose bititaye ku nyuguti nini/nto
            const userIn = document.getElementById("username").value.trim().toLowerCase();
            const passIn = document.getElementById("password").value.trim();
            const errorMsg = document.getElementById("errorMsg");

            if (userIn === "benitha" && passIn === "123") {
                document.getElementById("loginBox").style.display = "none";
                document.getElementById("dashboardBox").style.display = "block";
                document.body.style.display = "block"; 
                errorMsg.style.display = "none";
            } else {
                errorMsg.style.display = "block";
            }
        }

        function handleLogout() {
            document.getElementById("dashboardBox").style.display = "none";
            document.getElementById("loginBox").style.display = "block";
            document.body.style.display = "flex";
            document.getElementById("username").value = "";
            document.getElementById("password").value = "";
        }
    </script>
</body>
</html>



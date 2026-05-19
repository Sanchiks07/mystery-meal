const canvas = document.getElementById("gameCanvas");

if (canvas) {
    const ctx = canvas.getContext("2d");

    // ---------------- STATE ----------------
    let score = 0;
    let bestScore = Number(window.savedBestScore) || 0;
    let gameOver = false;
    let items = [];

    let mouseControl = false;
    let tabVisible = true;

    let spawnTimer = 0;
    let lastFrameTime = performance.now();

    document.getElementById("bestScore").innerText = bestScore;

    // ---------------- PLATFORM ----------------
    const platform = {
        x: canvas.width / 2 - 60,
        y: canvas.height - 30,
        width: 120,
        height: 20,
        speed: 8
    };

    // ---------------- CONTROLS ----------------
    const keys = {
        left: false,
        right: false
    };

    document.addEventListener("keydown", e => {
        if (mouseControl) return;
        if (e.key === "ArrowLeft") keys.left = true;
        if (e.key === "ArrowRight") keys.right = true;
    });

    document.addEventListener("keyup", e => {
        if (e.key === "ArrowLeft") keys.left = false;
        if (e.key === "ArrowRight") keys.right = false;
    });

    canvas.addEventListener("mousedown", e => {
        if (e.button !== 0) return;
        mouseControl = true;
    });

    document.addEventListener("mouseup", () => {
        mouseControl = false;
    });

    canvas.addEventListener("mousemove", e => {
        if (!mouseControl) return;

        const rect = canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;

        platform.x = mouseX - platform.width / 2;
    });

    canvas.addEventListener("dragstart", e => e.preventDefault());
    canvas.addEventListener("click", e => e.preventDefault());

    // ---------------- TAB VISIBILITY ----------------
    document.addEventListener("visibilitychange", () => {
        tabVisible = !document.hidden;

        if (!tabVisible) {
            keys.left = false;
            keys.right = false;
        }

        lastFrameTime = performance.now();
        spawnTimer = 0;
    });

    // ---------------- IMAGES ----------------
    function loadImage(src) {
        const img = new Image();
        img.src = src;
        return img;
    }

    const loadedFood = [
        loadImage("/images/apple.png"),
        loadImage("/images/burger.png"),
        loadImage("/images/cake.png"),
        loadImage("/images/cookie.png"),
        loadImage("/images/grape.png"),
        loadImage("/images/ice-cream.png"),
        loadImage("/images/lemon.png"),
        loadImage("/images/pizza.png"),
        loadImage("/images/sushi.png"),
        loadImage("/images/taco.png")
    ];

    const loadedBad = [
        loadImage("/images/bomb.png"),
        loadImage("/images/knife.png"),
        loadImage("/images/scissors.png"),
        loadImage("/images/saw.png"),
        loadImage("/images/high-heel.png")
    ];

    // ---------------- SPAWN ----------------
    function spawnItem() {
        if (gameOver || !tabVisible) return;
        const good = Math.random() > 0.3;
        const pool = good ? loadedFood : loadedBad;
        const img = pool[Math.floor(Math.random() * pool.length)];

        items.push({
            x: Math.random() * (canvas.width - 40),
            y: -50,

            width: 40,
            height: 40,

            hitboxWidth: 26,
            hitboxHeight: 26,

            speed: 2 + Math.random() * 2,
            good: good,
            image: img
        });
    }

    // ---------------- UPDATE ----------------
    function update() {
        if (gameOver) return;

        if (!mouseControl) {
            if (keys.left) platform.x -= platform.speed;
            if (keys.right) platform.x += platform.speed;
        }

        platform.x = Math.max(0, Math.min(canvas.width - platform.width, platform.x));

        for (let i = items.length - 1; i >= 0; i--) {
            const item = items[i];
            item.y += item.speed;

            const hitboxX =
                item.x + (item.width - item.hitboxWidth) / 2;

            const hitboxY =
                item.y + (item.height - item.hitboxHeight) / 2;

            const caught =
                hitboxX < platform.x + platform.width &&
                hitboxX + item.hitboxWidth > platform.x &&
                hitboxY < platform.y + platform.height &&
                hitboxY + item.hitboxHeight > platform.y;

            if (caught) {
                if (item.good) {
                    score++;
                    document.getElementById("score").innerText = score;

                    if (score > bestScore) {
                        bestScore = score;
                        document.getElementById("bestScore").innerText = bestScore;
                    }
                } else {
                    endGame();
                }

                items.splice(i, 1);
                continue;
            }

            if (item.y > canvas.height + 100) {
                items.splice(i, 1);
            }
        }
    }

    // ---------------- DRAW ----------------
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = "#1f3d2b";

        ctx.fillRect(
            platform.x,
            platform.y,
            platform.width,
            platform.height
        );

        for (const item of items) {
            if (!item.image.complete || item.image.naturalWidth === 0) continue;

            ctx.drawImage(
                item.image,
                item.x,
                item.y,
                item.width,
                item.height
            );
        }
    }

    // ---------------- LOOP ----------------
    function loop(now) {
        if (!gameOver) {
            const delta = now - lastFrameTime;
            lastFrameTime = now;

            if (tabVisible) {
                update();
                draw();
                spawnTimer += delta;

                if (spawnTimer >= 900) {
                    spawnItem();
                    spawnTimer = 0;
                }
            }

            requestAnimationFrame(loop);
        }
    }

    requestAnimationFrame(loop);

    // ---------------- SAVE ----------------
    async function saveHighscore() {
        try {
            await fetch("/save-score", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                },
                body: JSON.stringify({
                    score: bestScore
                })
            });
        } catch (err) {
            console.error(err);
        }
    }

    // ---------------- GAME OVER ----------------
    async function endGame() {
        if (gameOver) return;
        gameOver = true;
        document.getElementById("gameOver").style.display = "block";

        await saveHighscore();
    }

    // Click to dismiss game over popup and restart
    document.getElementById("gameOver").addEventListener("click", () => {
        document.getElementById("gameOver").style.display = "none";
        // Reset game state
        gameOver = false;
        score = 0;
        items = [];
        platform.x = canvas.width / 2 - 60; // Reset platform to center
        spawnTimer = 0;
        lastFrameTime = performance.now();
        document.getElementById("score").innerText = score;
        // Restart game loop
        requestAnimationFrame(loop);
    });
}
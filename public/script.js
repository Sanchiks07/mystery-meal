// ---------- GAME LOGIC ----------
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

let platform = {
    x: 150,
    y: 550,
    width: 100,
    height: 10,
    speed: 6
};

let items = [];
let score = 0;
let gameOver = false;

let keys = { left: false, right: false };

// Controls
document.addEventListener("keydown", e => {
    if (e.key === "ArrowLeft") keys.left = true;
    if (e.key === "ArrowRight") keys.right = true;
});

document.addEventListener("keyup", e => {
    if (e.key === "ArrowLeft") keys.left = false;
    if (e.key === "ArrowRight") keys.right = false;
});

// Spawn items
function spawnItem() {
    items.push({
        x: Math.random() * 360,
        y: 0,
        size: 20,
        speed: 2 + Math.random() * 3,
        good: Math.random() > 0.3
    });
}

// Update
function update() {
    if (gameOver) return;

    if (keys.left) platform.x -= platform.speed;
    if (keys.right) platform.x += platform.speed;

    platform.x = Math.max(0, Math.min(canvas.width - platform.width, platform.x));

    items.forEach((item, i) => {
        item.y += item.speed;

        if (
            item.y + item.size >= platform.y &&
            item.x < platform.x + platform.width &&
            item.x + item.size > platform.x
        ) {
            if (item.good) {
                score++;
                document.getElementById("score").innerText = score;
            } else {
                endGame();
            }
            items.splice(i, 1);
        }

        if (item.y > canvas.height) {
            items.splice(i, 1);
        }
    });
}

// Draw
function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    ctx.fillRect(platform.x, platform.y, platform.width, platform.height);

    items.forEach(item => {
        ctx.fillStyle = item.good ? "green" : "red";
        ctx.fillRect(item.x, item.y, item.size, item.size);
    });
}

// Loop
function loop() {
    update();
    draw();
    requestAnimationFrame(loop);
}

setInterval(spawnItem, 1000);

// End game
function endGame() {
    gameOver = true;
    document.getElementById("gameOver").style.display = "block";

    fetch("/save-score", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ score: score })
    });
}

loop();
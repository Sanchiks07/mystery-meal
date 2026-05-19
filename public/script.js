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
        loadImage("/images/game/apple.png"),
        loadImage("/images/game/burger.png"),
        loadImage("/images/game/cake.png"),
        loadImage("/images/game/cookie.png"),
        loadImage("/images/game/grape.png"),
        loadImage("/images/game/ice-cream.png"),
        loadImage("/images/game/lemon.png"),
        loadImage("/images/game/pizza.png"),
        loadImage("/images/game/sushi.png"),
        loadImage("/images/game/taco.png")
    ];

    const loadedBad = [
        loadImage("/images/game/bomb.png"),
        loadImage("/images/game/knife.png"),
        loadImage("/images/game/scissors.png"),
        loadImage("/images/game/saw.png"),
        loadImage("/images/game/high-heel.png")
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

// ============ INGREDIENT CHECKBOX PERSISTENCE ============

const INGREDIENTS_STORAGE_KEY = 'mystery-meal-selected-ingredients';
const SEARCH_STATE_HTML_KEY = 'mystery-meal-search-state-html';

function saveSelectedIngredients() {
    const selected = Array.from(
        document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]:checked')
    ).map(el => el.value);
    
    if (selected.length > 0) {
        localStorage.setItem(INGREDIENTS_STORAGE_KEY, JSON.stringify(selected));
    } else {
        localStorage.removeItem(INGREDIENTS_STORAGE_KEY);
    }
}

function restoreSelectedIngredients() {
    const storedValue = localStorage.getItem(INGREDIENTS_STORAGE_KEY);
    
    const inputs = document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]');
    inputs.forEach(input => (input.checked = false));
    
    if (!storedValue) {
        return;
    }

    let selected;
    try {
        selected = JSON.parse(storedValue);
    } catch (error) {
        return;
    }

    if (!Array.isArray(selected)) {
        return;
    }

    inputs.forEach(input => {
        if (selected.includes(input.value)) {
            input.checked = true;
        }
    });
}

function saveSearchState() {
    const container = document.getElementById('search-state-container');
    if (!container) {
        return;
    }

    const html = container.innerHTML.trim();

    if (html.length > 0) {
        localStorage.setItem(SEARCH_STATE_HTML_KEY, html);
    }
}

function restoreSearchState() {
    const container = document.getElementById('search-state-container');
    if (!container) {
        return;
    }

    if (container.children.length > 0) {
        return;
    }

    const storedHtml = localStorage.getItem(SEARCH_STATE_HTML_KEY);
    if (!storedHtml) {
        return;
    }

    container.innerHTML = storedHtml;
}

// Handle ingredient checkbox initialization and persistence
window.addEventListener('load', () => {
    const inputs = document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]');
    if (inputs.length === 0) {
        return;
    }

    const isCreatePage = window.location.pathname.includes('/recipes/create');

    if (isCreatePage) {
        // On create page: clear all storage and uncheck all boxes
        localStorage.removeItem(INGREDIENTS_STORAGE_KEY);
        localStorage.removeItem(SEARCH_STATE_HTML_KEY);
        inputs.forEach(input => (input.checked = false));
    } else {
        // On home/search pages: only restore if search results are visible
        // (meaning user came from a search, not a fresh page load)
        const selectedBox = document.querySelector('.selected-box');
        const recipesGrid = document.querySelector('.recipes-grid');
        
        if (selectedBox || recipesGrid?.children.length > 0) {
            // User searched or has results: restore from localStorage
            restoreSelectedIngredients();
            restoreSearchState();
        } else {
            // Fresh home page load: clear storage and uncheck all boxes
            localStorage.removeItem(INGREDIENTS_STORAGE_KEY);
            localStorage.removeItem(SEARCH_STATE_HTML_KEY);
            inputs.forEach(input => (input.checked = false));
        }
    }

    // Save state whenever a checkbox changes
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            saveSelectedIngredients();
            saveSearchState();
        });
    });

    // Handle tag form clicks (remove ingredient)
    const tagForms = document.querySelectorAll('.tag-form');
    tagForms.forEach(form => {
        const button = form.querySelector('.selected-tag');
        if (button) {
            button.addEventListener('click', e => {
                e.preventDefault();
                const ingredientName = button.textContent.trim().replace('×', '').trim();
                const checkbox = document.querySelector(
                    `input[type="checkbox"][name="ingredients[]"][value="${ingredientName}"]`
                );
                if (checkbox) {
                    checkbox.checked = false;
                    saveSelectedIngredients();
                    saveSearchState();
                }
                form.submit();
            });
        }
    });
});

// Also handle pageshow for back button navigation
window.addEventListener('pageshow', () => {
    const inputs = document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]');
    if (inputs.length === 0) {
        return;
    }

    const isCreatePage = window.location.pathname.includes('/recipes/create');

    if (isCreatePage) {
        // On create page: clear storage and uncheck boxes
        localStorage.removeItem(INGREDIENTS_STORAGE_KEY);
        localStorage.removeItem(SEARCH_STATE_HTML_KEY);
        inputs.forEach(input => (input.checked = false));
    } else {
        // On other pages: restore from storage
        restoreSelectedIngredients();
        restoreSearchState();
    }
});
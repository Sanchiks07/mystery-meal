// ---------- INGREDIENTS PERSISTENCE ----------
const INGREDIENTS_STORAGE_KEY = 'mystery-meal-selected-ingredients';
const SEARCH_STATE_HTML_KEY = 'mystery-meal-search-state-html';

function saveSelectedIngredients() {
    const selected = Array.from(document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]:checked')).map(el => el.value);
    localStorage.setItem(INGREDIENTS_STORAGE_KEY, JSON.stringify(selected));
    if (selected.length === 0) {
        localStorage.removeItem(SEARCH_STATE_HTML_KEY);
    }
}

function saveSearchState() {
    const container = document.getElementById('search-state-container');
    if (!container) {
        return;
    }

    const html = container.innerHTML.trim();

    if (html.length > 0) {
        localStorage.setItem(SEARCH_STATE_HTML_KEY, html);
    } else {
        localStorage.removeItem(SEARCH_STATE_HTML_KEY);
    }
}

function restoreSelectedIngredients() {
    const storedValue = localStorage.getItem(INGREDIENTS_STORAGE_KEY);
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

    document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]').forEach(input => {
        input.checked = selected.includes(input.value);
    });
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

    const checkedInputs = document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]:checked');
    if (checkedInputs.length === 0) {
        return;
    }

    container.innerHTML = storedHtml;
}

window.addEventListener('pageshow', () => {
    const inputs = document.querySelectorAll('input[type="checkbox"][name="ingredients[]"]');
    if (inputs.length > 0) {
        // Only restore if NOT on the create recipe page
        const isCreatePage = window.location.pathname.includes('/recipes/create');
        if (!isCreatePage) {
            restoreSelectedIngredients();
            restoreSearchState();
        } else {
            // On create page, clear persistence for a fresh start
            localStorage.removeItem(INGREDIENTS_STORAGE_KEY);
            localStorage.removeItem(SEARCH_STATE_HTML_KEY);
        }
        saveSearchState();
        inputs.forEach(input => input.addEventListener('change', () => {
            saveSelectedIngredients();
            saveSearchState();
        }));
    }

    const tagForms = document.querySelectorAll('.tag-form');
    tagForms.forEach(form => {
        const button = form.querySelector('.selected-tag');
        if (button) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                const ingredientName = button.textContent.trim().replace('×', '').trim();
                
                const checkbox = document.querySelector(`input[type="checkbox"][name="ingredients[]"][value="${ingredientName}"]`);
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
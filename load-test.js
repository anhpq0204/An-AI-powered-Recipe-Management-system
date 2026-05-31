/**
 * FRS Load Test — k6
 * Mô phỏng người dùng duyệt trang web bình thường
 *
 * Chạy: k6 run load-test.js
 * Chạy nặng hơn: k6 run --vus 50 --duration 1m load-test.js
 */
import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

// ── Custom metrics ───────────────────────────────────────
const errorRate   = new Rate('error_rate');
const apiDuration = new Trend('api_duration_ms', true);

// ── Test scenarios ────────────────────────────────────────
export const options = {
    scenarios: {
        // Scenario 1: Người dùng duyệt trang bình thường
        browse: {
            executor: 'ramping-vus',
            stages: [
                { duration: '20s', target: 10 },  // warm-up
                { duration: '40s', target: 30 },  // tăng tải
                { duration: '20s', target: 0 },   // cool-down
            ],
            gracefulRampDown: '10s',
        },
    },
    thresholds: {
        // 95% request phải trả về < 800ms
        http_req_duration: ['p(95)<800'],
        // Tỉ lệ lỗi phải < 1%
        error_rate: ['rate<0.01'],
        // API endpoint phải trả về < 500ms
        api_duration_ms: ['p(95)<500'],
    },
};

const BASE = 'http://frs.local';

// Danh sách recipe IDs thực tế
const RECIPE_IDS = [1, 2, 3];

// ── Main test function (1 lần = 1 "chuyến" của người dùng) ───
export default function () {
    const page = Math.random();

    if (page < 0.35) {
        // 35% - Xem trang chủ
        visitHomePage();
    } else if (page < 0.65) {
        // 30% - Duyệt danh sách recipes
        visitRecipesPage();
    } else if (page < 0.85) {
        // 20% - Xem chi tiết một recipe
        const rid = RECIPE_IDS[Math.floor(Math.random() * RECIPE_IDS.length)];
        visitRecipeDetail(rid);
    } else {
        // 15% - Tìm kiếm nguyên liệu qua API
        searchIngredients();
    }

    // Người dùng thực "đọc" trang 1-3 giây trước khi click tiếp
    sleep(1 + Math.random() * 2);
}

function visitHomePage() {
    const res = http.get(`${BASE}/index.php`, { tags: { page: 'home' } });
    const ok = check(res, {
        'home: status 200':          (r) => r.status === 200,
        'home: có recipe cards':     (r) => r.body.includes('recipe-card'),
        'home: có navigation':       (r) => r.body.includes('navbar'),
    });
    errorRate.add(!ok);
}

function visitRecipesPage() {
    const res = http.get(`${BASE}/recipes.php`, { tags: { page: 'recipes' } });
    const ok = check(res, {
        'recipes: status 200':       (r) => r.status === 200,
        'recipes: có recipe grid':   (r) => r.body.includes('recipes-grid'),
        'recipes: có filter box':    (r) => r.body.includes('ingredientFilter'),
    });
    errorRate.add(!ok);
}

function visitRecipeDetail(rid) {
    const res = http.get(`${BASE}/recipe-details.php?rid=${rid}`, { tags: { page: 'detail' } });
    const ok = check(res, {
        'detail: status 200':        (r) => r.status === 200,
        'detail: có recipe hero':    (r) => r.body.includes('recipe-hero-section'),
        'detail: có ingredients':    (r) => r.body.includes('ingredients-list'),
    });
    errorRate.add(!ok);
}

function searchIngredients() {
    const queries = ['chicken', 'egg', 'garlic', 'onion', 'rice'];
    const q = queries[Math.floor(Math.random() * queries.length)];

    const start = Date.now();
    const res = http.get(
        `${BASE}/api/search-ingredients.php?q=${q}`,
        { tags: { page: 'api-search' } }
    );
    apiDuration.add(Date.now() - start);

    const ok = check(res, {
        'api: status 200':    (r) => r.status === 200,
        'api: JSON valid':    (r) => {
            try { JSON.parse(r.body); return true; } catch { return false; }
        },
    });
    errorRate.add(!ok);
}

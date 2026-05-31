/**
 * FRS Stress Test — Tìm điểm giới hạn tối đa
 * Tăng dần từ 100 → 5000 users, ghi nhận khi nào server bắt đầu suy giảm
 */
import http from 'k6/http';
import { check, sleep } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';

const errorRate  = new Rate('error_rate');
const errorCount = new Counter('error_count');

export const options = {
    stages: [
        { duration: '30s', target: 100  },
        { duration: '30s', target: 500  },
        { duration: '30s', target: 1000 },
        { duration: '30s', target: 2000 },
        { duration: '30s', target: 3000 },
        { duration: '30s', target: 5000 },
        { duration: '30s', target: 0    },  // cool-down
    ],
    thresholds: {
        // Không hard-fail — muốn chạy hết để thấy toàn bộ đường cong
        http_req_duration: ['p(95)<5000'],
        error_rate: ['rate<0.5'],
    },
};

const BASE = 'http://frs.local';
const RECIPE_IDS = [1, 2, 3];

export default function () {
    const page = Math.random();
    let res;

    if (page < 0.4) {
        res = http.get(`${BASE}/index.php`, { tags: { page: 'home' } });
    } else if (page < 0.7) {
        res = http.get(`${BASE}/recipes.php`, { tags: { page: 'recipes' } });
    } else if (page < 0.85) {
        const rid = RECIPE_IDS[Math.floor(Math.random() * RECIPE_IDS.length)];
        res = http.get(`${BASE}/recipe-details.php?rid=${rid}`, { tags: { page: 'detail' } });
    } else {
        res = http.get(`${BASE}/api/search-ingredients.php?q=chicken`, { tags: { page: 'api' } });
    }

    const ok = check(res, { 'status 200': (r) => r.status === 200 });
    errorRate.add(!ok);
    if (!ok) errorCount.add(1);

    sleep(1 + Math.random() * 2);
}

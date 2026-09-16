// =====================================================================
// Laravel CSRF untuk Postman
// Taruh di tab "Pre-request Script" pada level Collection supaya
// otomatis berlaku untuk SEMUA request di dalam collection.
//
// Cara kerja:
// 1. GET/HEAD/OPTIONS tidak butuh CSRF token, jadi dilewati.
// 2. Kalau cookie XSRF-TOKEN sudah ada (dari request sebelumnya),
//    nilainya di-URL-decode lalu dipasang sebagai header X-XSRF-TOKEN.
// 3. Kalau cookie belum ada / sudah kedaluwarsa, script memanggil
//    /sanctum/csrf-cookie dulu untuk mendapatkan cookie baru.
//
// Catatan: Postman harus menyimpan cookie (cookie jar aktif, perilaku
// default). Login harus dilakukan SEBELUM request lain supaya session
// juga ikut tersimpan di cookie jar.
// =====================================================================

const method = pm.request.method.toUpperCase();

if (["GET", "HEAD", "OPTIONS"].includes(method)) {
    return;
}

const pasangHeaderCsrf = function (token) {
    // Cookie XSRF-TOKEN Laravel ter-URL-encode; header wajib bentuk aslinya.
    pm.request.headers.upsert({
        key: "X-XSRF-TOKEN",
        value: decodeURIComponent(token),
    });
};

const token = pm.cookies.get("XSRF-TOKEN");

if (token) {
    pasangHeaderCsrf(token);
    return;
}

// Cookie belum ada — minta yang baru lewat endpoint CSRF Sanctum.
const rawUrl = pm.request.url.toString();
const baseUrl = rawUrl.split("/").slice(0, 3).join("/");

pm.sendRequest(
    { url: baseUrl + "/sanctum/csrf-cookie", method: "GET" },
    function (error, response) {
        if (error) {
            console.error("Gagal mengambil XSRF-TOKEN:", error);
            return;
        }

        const tokenBaru = pm.cookies.get("XSRF-TOKEN");

        if (tokenBaru) {
            pasangHeaderCsrf(tokenBaru);
        } else {
            console.warn(
                "Cookie XSRF-TOKEN tidak ditemukan setelah memanggil /sanctum/csrf-cookie. " +
                "Periksa apakah base URL sama dengan URL request (cookie ikut domain) " +
                "dan cookie jar Postman aktif."
            );
        }
    }
);

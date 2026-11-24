import assert from "node:assert/strict";
import { execFileSync } from "node:child_process";
import { createServer } from "node:http";
import { readFileSync, statSync } from "node:fs";
import path from "node:path";
import test, { after, before } from "node:test";
import puppeteer from "puppeteer";

const root = process.cwd();
const publicRoot = path.join(root, "public");
const renderScript = path.join(
    root,
    "tests",
    "browser",
    "render-schedule-page.php",
);
const manifest = JSON.parse(
    readFileSync(path.join(publicRoot, "build", "manifest.json"), "utf8"),
);
const pages = {
    owner: execFileSync("php", [renderScript, "owner"], { encoding: "utf8" }),
    admin: execFileSync("php", [renderScript, "admin"], { encoding: "utf8" }),
};
let browser;

function servePublic(request, response) {
    const requestedPath = decodeURIComponent(
        new URL(request.url, "http://127.0.0.1").pathname,
    );
    const page =
        requestedPath === "/owner"
            ? pages.owner
            : requestedPath === "/admin"
              ? pages.admin
              : null;

    if (page) {
        const port = server.address().port;
        response.writeHead(200, { "Content-Type": "text/html; charset=utf-8" });
        const localAsset = (entry) =>
            `http://127.0.0.1:${port}/build/${manifest[entry].file}`;
        const html = page
            .replaceAll("http://warkop-ckb.test/", `http://127.0.0.1:${port}/`)
            .replace(
                /<script type="module" src="http:\/\/\[::1\]:5173\/@vite\/client"><\/script>/g,
                "",
            )
            .replaceAll(
                "http://[::1]:5173/resources/css/app.css",
                localAsset("resources/css/app.css"),
            )
            .replaceAll(
                "http://[::1]:5173/resources/js/app.js",
                localAsset("resources/js/app.js"),
            )
            .replaceAll(
                "http://[::1]:5173/resources/js/schedule/index.js",
                localAsset("resources/js/schedule/index.js"),
            );
        response.end(html);
        return;
    }

    const filePath = path.resolve(publicRoot, `.${requestedPath}`);
    if (
        !filePath.startsWith(publicRoot + path.sep) ||
        !statSync(filePath, { throwIfNoEntry: false })?.isFile()
    ) {
        response.writeHead(404);
        response.end();
        return;
    }

    const extension = path.extname(filePath);
    const type =
        extension === ".js"
            ? "text/javascript"
            : extension === ".css"
              ? "text/css"
              : "application/octet-stream";
    response.writeHead(200, { "Content-Type": type });
    response.end(readFileSync(filePath));
}

const server = createServer(servePublic);

async function allowAvatarPlaceholder(page) {
    await page.setRequestInterception(true);
    page.on("request", (request) => {
        if (request.url().startsWith("https://ui-avatars.com/")) {
            void request.respond({
                status: 200,
                contentType: "image/svg+xml",
                body: '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"><rect width="40" height="40" fill="#dce5f2"/></svg>',
            });
            return;
        }

        void request.continue();
    });
}

before(async () => {
    await new Promise((resolve) => server.listen(0, "127.0.0.1", resolve));
    browser = await puppeteer.launch({
        headless: true,
        args: ["--no-sandbox", "--disable-setuid-sandbox"],
    });
});

after(async () => {
    await browser?.close();
    await new Promise((resolve) => server.close(resolve));
});

test("owner can create, edit, delete, navigate, search, and filter preview schedules", async () => {
    const page = await browser.newPage({
        viewport: { width: 1440, height: 1000 },
    });
    page.setDefaultTimeout(10000);
    await allowAvatarPlaceholder(page);
    const browserErrors = [];
    page.on("pageerror", (error) => browserErrors.push(error.message));
    page.on("console", (message) => {
        if (message.type() === "error") browserErrors.push(message.text());
    });
    await page.goto(`http://127.0.0.1:${server.address().port}/owner`, {
        waitUntil: "networkidle0",
    });
    await page.waitForSelector("#schedule-week [data-schedule-id]");

    assert.equal(
        await page.$$eval("#btn-add-schedule", (elements) => elements.length),
        1,
    );
    assert.equal(
        await page.$$eval(
            "#schedule-week [data-schedule-id]",
            (elements) => elements.length,
        ),
        5,
    );

    await page.click("#btn-add-schedule");
    await page.waitForSelector("#schedule-form-modal:not(.hidden)");
    assert.equal(
        await page.$$eval(
            "#schedule-employee option",
            (elements) => elements.length,
        ),
        7,
    );
    await page.select("#schedule-employee", "101");
    assert.equal(
        await page.$eval("#schedule-position", (input) => input.value),
        "Barista",
    );
    await page.$eval("#schedule-shift-name", (input) => {
        input.value = "Tes Tambah";
    });
    await page.$eval("#schedule-date", (input) => {
        input.value =
            document.querySelector("#schedule-app").dataset.currentDate;
    });
    await page.$eval("#schedule-start-time", (input) => {
        input.value = "09:00";
    });
    await page.$eval("#schedule-end-time", (input) => {
        input.value = "17:00";
    });
    await page.click("#btn-save-schedule");
    await page.waitForSelector("#schedule-form-modal.hidden");
    await page.waitForFunction(
        () =>
            document.querySelectorAll("#schedule-week [data-schedule-id]")
                .length === 6,
    );

    const scheduleId = await page.$eval(
        "#schedule-week [data-schedule-id]",
        (button) => button.dataset.scheduleId,
    );
    await page.click(`#schedule-week [data-schedule-id="${scheduleId}"]`);
    await page.waitForSelector("#schedule-detail-modal:not(.hidden)");
    await page.click("#btn-edit-schedule");
    await page.waitForSelector("#schedule-form-modal:not(.hidden)");
    await page.$eval("#schedule-shift-name", (input) => {
        input.value = "Tes Edit";
    });
    await page.click("#btn-save-schedule");
    await page.waitForSelector("#schedule-form-modal.hidden");
    await page.click(`#schedule-week [data-schedule-id="${scheduleId}"]`);
    await page.waitForFunction(() =>
        document
            .querySelector("#schedule-detail-content")
            ?.textContent.includes("Tes Edit"),
    );

    await page.click("#btn-delete-schedule");
    await page.waitForSelector(".swal2-confirm");
    await page.click(".swal2-confirm");
    await page.waitForFunction(
        () =>
            document.querySelectorAll("#schedule-week [data-schedule-id]")
                .length === 5,
    );

    const previousWeek = await page.$eval(
        "#week-label",
        (label) => label.textContent,
    );
    await page.click("#btn-next-week");
    await page.waitForFunction(
        (previous) =>
            document.querySelector("#week-label")?.textContent !== previous,
        {},
        previousWeek,
    );
    await page.select("#position-filter", "Cashier");
    await page.click("#btn-current-week");
    await page.waitForFunction(
        () =>
            document.querySelectorAll("#schedule-week [data-schedule-id]")
                .length === 2,
    );
    await page.type("#schedule-search", "Bima");
    await page.waitForFunction(
        () =>
            document.querySelectorAll("#schedule-week [data-schedule-id]")
                .length === 2,
    );
    assert.deepEqual(browserErrors, []);
    await page.close();
});

test("admin can search, filter, navigate, and view details without mutation controls", async () => {
    const page = await browser.newPage({
        viewport: { width: 390, height: 844 },
    });
    page.setDefaultTimeout(10000);
    await allowAvatarPlaceholder(page);
    const browserErrors = [];
    page.on("pageerror", (error) => browserErrors.push(error.message));
    page.on("console", (message) => {
        if (message.type() === "error") browserErrors.push(message.text());
    });
    await page.goto(`http://127.0.0.1:${server.address().port}/admin`, {
        waitUntil: "networkidle0",
    });
    await page.waitForSelector("#schedule-week [data-schedule-id]");
    assert.equal(
        await page.evaluate(
            () => document.documentElement.scrollWidth <= window.innerWidth,
        ),
        true,
    );

    assert.equal(
        await page.$$eval("#btn-add-schedule", (elements) => elements.length),
        0,
    );
    assert.equal(
        await page.$$eval("#schedule-form", (elements) => elements.length),
        0,
    );
    assert.equal(
        await page.$$eval("#btn-edit-schedule", (elements) => elements.length),
        0,
    );
    assert.equal(
        await page.$$eval(
            "#btn-delete-schedule",
            (elements) => elements.length,
        ),
        0,
    );

    await page.select("#position-filter", "Barista");
    await page.waitForFunction(
        () =>
            document.querySelectorAll("#schedule-week [data-schedule-id]")
                .length === 3,
    );
    await page.click("#schedule-week [data-schedule-id]");
    await page.waitForSelector("#schedule-detail-modal:not(.hidden)");
    assert.equal(
        await page.$$eval(
            "#schedule-detail-actions",
            (elements) => elements.length,
        ),
        0,
    );
    await page.click("#btn-next-week");
    await page.click("#btn-current-week");
    await page.type("#schedule-search", "Bima");
    await page.waitForSelector("#schedule-status:not(.hidden)");
    assert.deepEqual(browserErrors, []);
    await page.close();
});

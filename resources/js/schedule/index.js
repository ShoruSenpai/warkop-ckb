import { $, escapeHtml, toggleModal } from "../utils/dom.js";
import { ckbColor } from "../utils/theme.js";
import { employeeScheduleService } from "./service.js";
import { openScheduleForm } from "./form.js";
import {
    getWeekDates,
    getWeekRange,
    normalizePosition,
    parseDateString,
    summarizeSchedules,
} from "./logic.js";

const app = $("schedule-app");
const canManage = app?.dataset.role === "owner";
const today = parseDateString(app?.dataset.currentDate) ?? new Date();
const state = {
    week: getWeekRange(today),
    employees: [],
    schedules: [],
    selectedSchedule: null,
    requestNumber: 0,
};

const dateFormatter = new Intl.DateTimeFormat("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
});
const weekdayFormatter = new Intl.DateTimeFormat("id-ID", { weekday: "short" });
const fullDateFormatter = new Intl.DateTimeFormat("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
});

function dateFromString(value) {
    return parseDateString(value);
}

function displayDate(value) {
    const date = dateFromString(value);

    return date ? dateFormatter.format(date) : "Tanggal tidak tersedia";
}

function displayPosition(position) {
    return normalizePosition(position) === "cashier"
        ? "Kasir"
        : String(position || "Posisi belum diisi");
}

function positionTone(position) {
    switch (normalizePosition(position)) {
        case "barista":
            return "border-ckb-accent/25 bg-ckb-accent-container text-ckb-on-accent-container";
        case "cashier":
            return "border-ckb-success/25 bg-ckb-success-container text-ckb-on-success-container";
        case "kitchen":
            return "border-ckb-warning/25 bg-ckb-warning-container text-ckb-on-warning-container";
        default:
            return "border-ckb-outline-variant/70 bg-ckb-surface-container-low text-ckb-on-surface-variant";
    }
}

function setLoading(isLoading, message = "Memuat jadwal...") {
    const status = $("schedule-status");
    const scheduleWeek = $("schedule-week");

    if (isLoading) {
        status.classList.remove("hidden");
        status.innerHTML = `<i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>${escapeHtml(message)}`;
        scheduleWeek.classList.add("hidden");
    } else {
        status.classList.add("hidden");
        scheduleWeek.classList.remove("hidden");
    }
}

function renderWeekLabel() {
    const start = dateFromString(state.week.from);
    const end = dateFromString(state.week.to);

    if (!start || !end) return;

    const sameMonth =
        start.getMonth() === end.getMonth() &&
        start.getFullYear() === end.getFullYear();
    const startText = sameMonth
        ? String(start.getDate())
        : dateFormatter.format(start);
    const endText = dateFormatter.format(end);

    $("week-label").textContent = `${startText} – ${endText}`;
}

function readFilters() {
    return {
        from: state.week.from,
        to: state.week.to,
        position: $("position-filter").value,
        q: $("schedule-search").value.trim(),
    };
}

function renderActiveFilter() {
    const filter = $("active-filter");
    const labels = [];
    const position = $("position-filter").selectedOptions[0]?.textContent;
    const search = $("schedule-search").value.trim();

    if ($("position-filter").value) labels.push(position);
    if (search) labels.push(`Pencarian “${search}”`);

    filter.classList.toggle("hidden", labels.length === 0);
    filter.classList.toggle("flex", labels.length > 0);
    $("active-filter-label").textContent = labels.join(" · ");
}

function renderSummary(schedules) {
    const summary = summarizeSchedules(schedules);

    $("summary-employees").textContent = summary.employees;
    $("summary-assignments").textContent = summary.assignments;
    $("summary-barista").textContent = summary.barista;
    $("summary-cashier").textContent = summary.cashier;
    $("summary-kitchen").textContent = summary.kitchen;
}

function renderPositionOptions(employees) {
    const select = $("position-filter");
    const selected = select.value;
    const positions = [
        ...new Set(
            employees
                .map((employee) => String(employee.position ?? "").trim())
                .filter(Boolean),
        ),
    ].sort((a, b) => a.localeCompare(b, "id"));

    select.innerHTML =
        '<option value="">Semua Posisi</option>' +
        positions
            .map(
                (position) =>
                    `<option value="${escapeHtml(position)}">${escapeHtml(displayPosition(position))}</option>`,
            )
            .join("");

    if (positions.includes(selected)) select.value = selected;
}

function formatTime(value) {
    return String(value ?? "").slice(0, 5);
}

function renderScheduleCard(schedule) {
    const employee = schedule.employee ?? {};
    const time = `${formatTime(schedule.start_time)}–${formatTime(schedule.end_time)}`;

    return `
        <button type="button" data-schedule-id="${escapeHtml(schedule.id)}" class="group w-full rounded-lg border ${positionTone(employee.position)} p-2.5 text-left transition hover:border-ckb-accent/50 hover:shadow-surface focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none">
            <span class="block truncate text-xs font-semibold">${escapeHtml(employee.full_name || "Karyawan tidak tersedia")}</span>
            <span class="mt-1.5 block truncate text-[10px] opacity-80">${escapeHtml(schedule.shift_name || "Shift")}</span>
            <span class="mt-1.5 flex items-center gap-1.5 text-[10px] font-medium">
                <i class="fas fa-clock" aria-hidden="true"></i>
                ${escapeHtml(time)}
            </span>
        </button>
    `;
}

function renderDay(date, schedules) {
    const dayDate = dateFromString(date);
    const dateLabel = dayDate ? weekdayFormatter.format(dayDate) : "Hari";
    const isToday = date === app.dataset.currentDate;
    const daySchedules = schedules.filter(
        (schedule) => schedule.schedule_date === date,
    );
    const byPosition = new Map();

    for (const schedule of daySchedules) {
        const position =
            String(schedule.employee?.position ?? "Lainnya").trim() ||
            "Lainnya";
        byPosition.set(position, [
            ...(byPosition.get(position) ?? []),
            schedule,
        ]);
    }

    const positionOrder = (position) => {
        const key = normalizePosition(position);

        return key === "barista"
            ? 0
            : key === "cashier"
              ? 1
              : key === "kitchen"
                ? 2
                : 3;
    };
    const groups = [...byPosition.entries()].sort(([left], [right]) => {
        return (
            positionOrder(left) - positionOrder(right) ||
            left.localeCompare(right, "id")
        );
    });
    const assignments = groups.length
        ? groups
              .map(
                  ([position, items]) => `
            <section class="space-y-1.5" aria-label="Posisi ${escapeHtml(displayPosition(position))}">
                <h4 class="flex items-center justify-between px-0.5 text-[10px] font-semibold text-ckb-on-surface-variant">
                    <span>${escapeHtml(displayPosition(position))}</span>
                    <span>${items.length}</span>
                </h4>
                <div class="space-y-1.5">${items.map(renderScheduleCard).join("")}</div>
            </section>
        `,
              )
              .join("")
        : '<p class="rounded-lg border border-dashed border-ckb-outline-variant/70 px-2 py-3 text-center text-[10px] text-ckb-on-surface-variant">Belum ada penugasan</p>';

    return `
        <article class="min-w-0 rounded-xl border ${isToday ? "border-ckb-accent/50" : "border-ckb-outline-variant/50"} bg-ckb-surface p-2.5 shadow-surface sm:p-3">
            <header class="mb-3 flex items-start justify-between gap-2 border-b border-ckb-outline-variant/40 pb-2.5">
                <div>
                    <p class="text-xs font-bold capitalize text-ckb-primary">${escapeHtml(dateLabel)}</p>
                    <p class="mt-0.5 text-[10px] text-ckb-on-surface-variant">${escapeHtml(dayDate ? dateFormatter.format(dayDate) : "")}</p>
                </div>
                ${isToday ? '<span class="rounded-full bg-ckb-primary-container px-2 py-1 text-[9px] font-semibold text-ckb-on-primary-container">Hari ini</span>' : ""}
            </header>
            <div class="space-y-3">${assignments}</div>
        </article>
    `;
}

function renderSchedules(schedules) {
    const scheduleWeek = $("schedule-week");
    const weekDates = getWeekDates(state.week.from);

    scheduleWeek.innerHTML = weekDates
        .map((date) => renderDay(date, schedules))
        .join("");

    if (schedules.length === 0) {
        const hasFilter = Boolean(
            $("position-filter").value || $("schedule-search").value.trim(),
        );
        const title = hasFilter
            ? "Tidak ada jadwal yang cocok"
            : "Belum ada jadwal minggu ini";
        const message = hasFilter
            ? "Coba ubah kata pencarian atau hapus filter posisi."
            : "Belum ada penugasan pada rentang tanggal ini.";

        $("schedule-status").innerHTML = `
            <i class="fas fa-calendar text-xl text-ckb-on-surface-variant" aria-hidden="true"></i>
            <h3 class="mt-3 text-sm font-bold text-ckb-on-surface">${title}</h3>
            <p class="mx-auto mt-1 max-w-md text-xs text-ckb-on-surface-variant">${message}</p>
        `;
        $("schedule-status").classList.remove("hidden");
        scheduleWeek.classList.add("hidden");
    } else {
        $("schedule-status").classList.add("hidden");
        scheduleWeek.classList.remove("hidden");
    }
}

function showLoadError(error) {
    const message =
        error?.data?.message || error?.message || "Jadwal tidak dapat dimuat.";

    $("schedule-status").innerHTML = `
        <i class="fas fa-circle-exclamation text-xl text-ckb-error" aria-hidden="true"></i>
        <h3 class="mt-3 text-sm font-bold text-ckb-on-surface">Jadwal belum dapat dimuat</h3>
        <p class="mx-auto mt-1 max-w-lg text-xs text-ckb-on-surface-variant">${escapeHtml(message)}</p>
        <button type="button" id="btn-retry-schedule" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-ckb-outline-variant px-4 text-xs font-semibold text-ckb-primary hover:bg-ckb-surface-container-low">
            <i class="fas fa-rotate-right" aria-hidden="true"></i> Coba lagi
        </button>
    `;
    $("schedule-status").classList.remove("hidden");
    $("schedule-week").classList.add("hidden");
}

function refreshCurrentFilters() {
    renderActiveFilter();
    return loadSchedules();
}

async function loadSchedules() {
    const requestNumber = ++state.requestNumber;
    setLoading(true);
    renderWeekLabel();

    try {
        const result =
            await employeeScheduleService.getSchedules(readFilters());
        if (requestNumber !== state.requestNumber) return;

        state.schedules = Array.isArray(result) ? result : result.data;
        renderSummary(state.schedules);
        renderSchedules(state.schedules);
    } catch (error) {
        if (requestNumber === state.requestNumber) showLoadError(error);
    }
}

function showToast(icon, title) {
    return Swal.fire({
        toast: true,
        position: "top-end",
        icon,
        title,
        showConfirmButton: false,
        timer: 2600,
        timerProgressBar: true,
    });
}

function renderScheduleDetail(schedule) {
    const employee = schedule.employee ?? {};
    const rows = [
        ["Karyawan", employee.full_name || "Karyawan tidak tersedia"],
        ["Kode Karyawan", employee.employee_code || "—"],
        ["Posisi", displayPosition(employee.position)],
        ["Nama Shift", schedule.shift_name || "—"],
        [
            "Tanggal",
            dateFromString(schedule.schedule_date)
                ? fullDateFormatter.format(
                      dateFromString(schedule.schedule_date),
                  )
                : "—",
        ],
        [
            "Jam Kerja",
            `${formatTime(schedule.start_time)}–${formatTime(schedule.end_time)}`,
        ],
    ];

    $("schedule-detail-content").innerHTML = `
        <div class="flex items-start gap-3 rounded-xl border ${positionTone(employee.position)} p-3.5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-ckb-surface/70 text-sm font-bold">${escapeHtml(
                String(employee.full_name ?? "K")
                    .trim()
                    .slice(0, 1)
                    .toLocaleUpperCase("id-ID"),
            )}</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold">${escapeHtml(employee.full_name || "Karyawan tidak tersedia")}</p>
                <p class="mt-1 text-xs opacity-80">${escapeHtml(displayPosition(employee.position))}</p>
            </div>
        </div>
        <dl class="grid grid-cols-1 gap-x-5 gap-y-3 sm:grid-cols-2">
            ${rows
                .map(
                    ([label, value]) => `
                <div class="min-w-0">
                    <dt class="text-[10px] font-medium text-ckb-on-surface-variant">${escapeHtml(label)}</dt>
                    <dd class="mt-1 truncate text-xs font-semibold text-ckb-on-surface">${escapeHtml(value)}</dd>
                </div>
            `,
                )
                .join("")}
        </dl>
    `;
    state.selectedSchedule = schedule;
    toggleModal("schedule-detail-modal", true);
}

async function openScheduleDetail(id) {
    try {
        const result = await employeeScheduleService.getScheduleById(id);
        renderScheduleDetail(result.data ?? result);
    } catch (error) {
        await Swal.fire({
            icon: "error",
            title: "Detail tidak tersedia",
            text: error?.message || "Jadwal tidak dapat dibuka.",
            confirmButtonText: "Mengerti",
        });
    }
}

async function showOwnerScheduleForm(schedule = null) {
    if (!canManage) return;

    try {
        const result = await employeeScheduleService.getEmployeesForSchedule(
            schedule ? {} : { is_active: true },
        );
        state.employees = result.data ?? result;
        openScheduleForm({
            schedule,
            employeeData: state.employees,
            save: saveSchedule,
        });
    } catch (error) {
        await Swal.fire({
            icon: "error",
            title: "Data karyawan belum tersedia",
            text: error?.message || "Pilihan karyawan tidak dapat dimuat.",
            confirmButtonText: "Mengerti",
        });
    }
}

async function saveSchedule(payload) {
    if (!canManage) throw new Error("Aksi ini hanya tersedia untuk Owner.");

    const result = payload.id
        ? await employeeScheduleService.updateSchedule(payload.id, payload)
        : await employeeScheduleService.createSchedule(payload);
    const scheduleDate = parseDateString(payload.schedule_date);

    if (scheduleDate) state.week = getWeekRange(scheduleDate);
    await loadSchedules();

    if (result.source === "local-fixture") {
        await showToast(
            "success",
            "Pratinjau diperbarui. Perubahan hanya tersimpan sementara.",
        );
    } else {
        await showToast("success", "Jadwal berhasil disimpan.");
    }
}

async function deleteSelectedSchedule() {
    if (!canManage || !state.selectedSchedule || deleteInProgress) return;

    const button = $("btn-delete-schedule");
    deleteInProgress = true;
    if (button) button.disabled = true;

    try {
        const result = await Swal.fire({
            icon: "warning",
            title: "Hapus jadwal ini?",
            text: "Jadwal akan dihapus dari data pratinjau sementara.",
            showCancelButton: true,
            confirmButtonText: "Hapus Jadwal",
            cancelButtonText: "Batal",
            confirmButtonColor: ckbColor("error"),
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: async () => {
                try {
                    await employeeScheduleService.deleteSchedule(
                        state.selectedSchedule.id,
                    );
                    return true;
                } catch (error) {
                    Swal.showValidationMessage(
                        error?.data?.message ||
                            error?.message ||
                            "Jadwal belum dapat dihapus.",
                    );
                    return false;
                }
            },
        });

        if (result.isConfirmed) {
            toggleModal("schedule-detail-modal", false);
            await loadSchedules();
            await showToast(
                "success",
                "Jadwal dihapus dari pratinjau. Database tidak berubah.",
            );
        }
    } finally {
        deleteInProgress = false;
        if (button) button.disabled = false;
    }
}

let deleteInProgress = false;
let searchTimer = null;

$("btn-prev-week").addEventListener("click", () => {
    const start = dateFromString(state.week.from);
    start.setDate(start.getDate() - 7);
    state.week = getWeekRange(start);
    void loadSchedules();
});

$("btn-next-week").addEventListener("click", () => {
    const start = dateFromString(state.week.from);
    start.setDate(start.getDate() + 7);
    state.week = getWeekRange(start);
    void loadSchedules();
});

$("btn-current-week").addEventListener("click", () => {
    state.week = getWeekRange(today);
    void loadSchedules();
});

$("position-filter").addEventListener(
    "change",
    () => void refreshCurrentFilters(),
);

$("schedule-search").addEventListener("input", () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => void refreshCurrentFilters(), 180);
});

$("btn-clear-filters").addEventListener("click", () => {
    $("position-filter").value = "";
    $("schedule-search").value = "";
    void refreshCurrentFilters();
});

$("schedule-week").addEventListener("click", (event) => {
    const button = event.target.closest("[data-schedule-id]");
    if (button) void openScheduleDetail(button.dataset.scheduleId);
});

$("schedule-status").addEventListener("click", (event) => {
    if (event.target.closest("#btn-retry-schedule")) void loadSchedules();
});

$("schedule-detail-modal")
    .querySelectorAll("[data-modal-close]")
    .forEach((button) => {
        button.addEventListener("click", () =>
            toggleModal("schedule-detail-modal", false),
        );
    });

$("schedule-detail-modal").addEventListener("click", (event) => {
    if (event.target === event.currentTarget)
        toggleModal("schedule-detail-modal", false);
});

if (canManage) {
    $("btn-add-schedule")?.addEventListener(
        "click",
        () => void showOwnerScheduleForm(),
    );
    $("btn-edit-schedule")?.addEventListener("click", () => {
        if (!state.selectedSchedule) return;
        const schedule = state.selectedSchedule;
        toggleModal("schedule-detail-modal", false);
        void showOwnerScheduleForm(schedule);
    });
    $("btn-delete-schedule")?.addEventListener(
        "click",
        () => void deleteSelectedSchedule(),
    );
}

Promise.all([employeeScheduleService.getEmployeesForSchedule()])
    .then(([result]) => {
        state.employees = result.data ?? result;
        renderPositionOptions(state.employees);
        return loadSchedules();
    })
    .catch(showLoadError);

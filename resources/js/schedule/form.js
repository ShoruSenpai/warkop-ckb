import { $, escapeHtml, toggleModal } from "../utils/dom.js";
import { validateSchedule } from "./logic.js";

const modalId = "schedule-form-modal";
const form = $("schedule-form");
const employeeSelect = $("schedule-employee");
const positionInput = $("schedule-position");
const submitButton = $("btn-save-schedule");
let onSave = null;
let employees = [];
let saving = false;
let inactiveEmployeeId = null;

function field(name) {
    return form.elements.namedItem(name);
}

function showFieldErrors(errors = {}) {
    for (const name of [
        "employee_id",
        "shift_name",
        "schedule_date",
        "start_time",
        "end_time",
    ]) {
        const message = errors[name] ?? "";
        const error = $(`schedule-error-${name}`);
        const input = field(name);

        error.textContent = message;
        error.classList.toggle("hidden", !message);
        input?.setAttribute("aria-invalid", String(Boolean(message)));
    }
}

function fillEmployees(selectedId = null) {
    const choices = employees.filter(
        (employee) =>
            employee.is_active || String(employee.id) === String(selectedId),
    );
    const optionMarkup = choices
        .map((employee) => {
            const inactive = employee.is_active
                ? ""
                : " · nonaktif (jadwal lama)";
            const label = `${employee.full_name} · ${employee.position}${inactive}`;

            return `<option value="${escapeHtml(employee.id)}">${escapeHtml(label)}</option>`;
        })
        .join("");

    employeeSelect.innerHTML = `<option value="">Pilih karyawan aktif</option>${optionMarkup}`;
    employeeSelect.value = selectedId ? String(selectedId) : "";
    syncPosition();
}

function syncPosition() {
    const employee = employees.find(
        (item) => String(item.id) === employeeSelect.value,
    );
    positionInput.value = employee?.position ?? "";
}

export function openScheduleForm({ schedule = null, employeeData = [], save }) {
    employees = employeeData;
    onSave = save;
    saving = false;
    inactiveEmployeeId = schedule?.employee_id ?? null;
    form.reset();
    showFieldErrors();
    $("schedule-form-error").classList.add("hidden");
    $("schedule-form-title").textContent = schedule
        ? "Edit Jadwal"
        : "Tambah Jadwal";
    submitButton.innerHTML = `<i class="fas fa-save" aria-hidden="true"></i> ${schedule ? "Simpan Perubahan" : "Simpan Jadwal"}`;
    field("id").value = schedule?.id ?? "";
    fillEmployees(schedule?.employee_id);
    field("shift_name").value = schedule?.shift_name ?? "";
    field("schedule_date").value = schedule?.schedule_date ?? "";
    field("start_time").value = String(schedule?.start_time ?? "").slice(0, 5);
    field("end_time").value = String(schedule?.end_time ?? "").slice(0, 5);
    toggleModal(modalId, true);
}

if (form) {
    employeeSelect.addEventListener("change", syncPosition);

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        if (saving || typeof onSave !== "function") return;

        const payload = {
            employee_id: field("employee_id").value,
            shift_name: field("shift_name").value.trim(),
            schedule_date: field("schedule_date").value,
            start_time: field("start_time").value,
            end_time: field("end_time").value,
        };
        const errors = validateSchedule(payload, employees, {
            allowInactiveEmployeeId: inactiveEmployeeId,
        });

        showFieldErrors(errors);

        if (Object.keys(errors).length) {
            form.querySelector(`[name="${Object.keys(errors)[0]}"]`)?.focus();
            return;
        }

        const id = field("id").value;
        saving = true;
        submitButton.disabled = true;
        submitButton.innerHTML =
            '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Menyimpan...';
        $("schedule-form-error").classList.add("hidden");

        try {
            await onSave(id ? { id, ...payload } : payload);
            toggleModal(modalId, false);
        } catch (error) {
            const apiErrors = error?.data?.errors;

            if (apiErrors) {
                showFieldErrors(
                    Object.fromEntries(
                        Object.entries(apiErrors).map(([name, messages]) => [
                            name,
                            Array.isArray(messages) ? messages[0] : messages,
                        ]),
                    ),
                );
            }

            const message =
                error?.data?.message ??
                error?.message ??
                "Jadwal belum dapat disimpan.";
            $("schedule-form-error").innerHTML = escapeHtml(message);
            $("schedule-form-error").classList.remove("hidden");
        } finally {
            saving = false;
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-save" aria-hidden="true"></i> ${id ? "Simpan Perubahan" : "Simpan Jadwal"}`;
        }
    });

    document
        .querySelectorAll(`#${modalId} [data-modal-close]`)
        .forEach((button) => {
            button.addEventListener("click", () => toggleModal(modalId, false));
        });

    $(modalId).addEventListener("click", (event) => {
        if (event.target === event.currentTarget) toggleModal(modalId, false);
    });
}

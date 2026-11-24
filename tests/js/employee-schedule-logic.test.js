import test from "node:test";
import assert from "node:assert/strict";
import {
    getWeekRange,
    summarizeSchedules,
    filterSchedules,
    validateSchedule,
} from "../../resources/js/schedule/logic.js";

test("weekly range starts on Monday and includes Sunday", () => {
    assert.deepEqual(getWeekRange(new Date(2026, 10, 25)), {
        from: "2026-11-23",
        to: "2026-11-29",
    });
});

test("summary counts unique scheduled employees and displayed assignments", () => {
    const schedules = [
        { employee_id: 10, employee: { position: "Barista" } },
        { employee_id: 10, employee: { position: "Barista" } },
        { employee_id: 11, employee: { position: "Cashier" } },
    ];

    assert.deepEqual(summarizeSchedules(schedules), {
        employees: 2,
        assignments: 3,
        barista: 2,
        cashier: 1,
        kitchen: 0,
    });
});

test("schedule filters match employee name and position without mutating source data", () => {
    const schedules = [
        {
            employee_id: 1,
            schedule_date: "2026-11-24",
            employee: { full_name: "Ayu", position: "Barista" },
        },
        {
            employee_id: 2,
            schedule_date: "2026-11-25",
            employee: { full_name: "Bima", position: "Cashier" },
        },
    ];

    const filtered = filterSchedules(schedules, {
        from: "2026-11-23",
        to: "2026-11-29",
        position: "Barista",
        search: "ayu",
    });

    assert.equal(filtered.length, 1);
    assert.equal(filtered[0].employee.full_name, "Ayu");
    assert.equal(schedules.length, 2);
});

test("schedule validation rejects overnight times and unknown or inactive employees", () => {
    const employees = [
        { id: 1, full_name: "Ayu", position: "Barista", is_active: true },
        { id: 2, full_name: "Bima", position: "Cashier", is_active: false },
    ];
    const errors = validateSchedule(
        {
            employee_id: 99,
            shift_name: "Pagi",
            schedule_date: "2026-11-24",
            start_time: "22:00",
            end_time: "06:00",
        },
        employees,
    );

    assert.deepEqual(errors, {
        employee_id: "Pilih karyawan aktif.",
        end_time: "Jam selesai harus setelah jam mulai.",
    });
    assert.deepEqual(
        validateSchedule(
            {
                employee_id: 2,
                shift_name: "Malam",
                schedule_date: "2026-11-24",
                start_time: "08:00",
                end_time: "16:00",
            },
            employees,
        ),
        { employee_id: "Pilih karyawan aktif." },
    );
});

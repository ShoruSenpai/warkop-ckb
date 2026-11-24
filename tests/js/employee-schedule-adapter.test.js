import test from "node:test";
import assert from "node:assert/strict";
import { FixtureAdapter } from "../../resources/js/schedule/mock-adapter.js";

test("fixture CRUD changes only its in-memory collection and resolves employee records", async () => {
    const adapter = new FixtureAdapter(new Date(2026, 10, 25));
    const created = await adapter.createSchedule({
        employee_id: 101,
        shift_name: "Pagi",
        schedule_date: "2026-11-29",
        start_time: "07:00",
        end_time: "15:00",
    });

    assert.equal(created.source, "local-fixture");
    assert.equal(created.data.employee.full_name, "Ayu Lestari");

    const edited = await adapter.updateSchedule(created.data.id, {
        employee_id: 102,
        shift_name: "Sore",
        schedule_date: "2026-11-29",
        start_time: "14:00",
        end_time: "22:00",
    });
    assert.equal(edited.data.employee.full_name, "Bima Saputra");
    assert.equal(edited.data.shift_name, "Sore");

    await adapter.deleteSchedule(created.data.id);
    await assert.rejects(
        adapter.getScheduleById(created.data.id),
        /tidak ditemukan/,
    );
    assert.equal(adapter.schedules.length, 5);
});

test("new schedule choices include active employees only when requested", async () => {
    const adapter = new FixtureAdapter(new Date(2026, 10, 25));
    const result = await adapter.getEmployeesForSchedule({ is_active: true });

    assert.equal(result.data.length, 6);
    assert.ok(result.data.every((employee) => employee.is_active));
});

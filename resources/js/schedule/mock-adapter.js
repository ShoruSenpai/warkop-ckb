import { filterSchedules, getWeekDates, getWeekRange } from "./logic.js";

export class FixtureAdapter {
    constructor(anchorDate = new Date()) {
        const week = getWeekRange(anchorDate);
        const weekDates = getWeekDates(week.from);

        this.employees = [
            {
                id: 101,
                employee_code: "DEMO-01",
                full_name: "Ayu Lestari",
                position: "Barista",
                is_active: true,
            },
            {
                id: 102,
                employee_code: "DEMO-02",
                full_name: "Bima Saputra",
                position: "Cashier",
                is_active: true,
            },
            {
                id: 104,
                employee_code: "DEMO-04",
                full_name: "Raka Pratama",
                position: "Barista",
                is_active: true,
            },
            {
                id: 105,
                employee_code: "DEMO-05",
                full_name: "Dina Maharani",
                position: "Cashier",
                is_active: false,
            },
            {
                id: 106,
                employee_code: "DEMO-06",
                full_name: "Contoh Administrator",
                position: "Admin",
                is_active: true,
            },
            {
                id: 107,
                employee_code: "DEMO-07",
                full_name: "Contoh Pengembang",
                position: "Developer",
                is_active: true,
            },
            {
                id: 108,
                employee_code: "DEMO-08",
                full_name: "Contoh Pemilik",
                position: "Owner",
                is_active: true,
            },
        ];

        const assignments = [
            [101, 0, "Pagi", "07:00", "15:00"],
            [102, 0, "Pagi", "07:00", "15:00"],
            [104, 2, "Pagi", "08:00", "16:00"],
            [101, 3, "Sore", "14:00", "22:00"],
            [102, 4, "Sore", "14:00", "22:00"],
        ];

        this.schedules = assignments.map(
            (
                [employeeId, dateIndex, shiftName, startTime, endTime],
                index,
            ) => ({
                id: index + 1,
                employee_id: employeeId,
                shift_name: shiftName,
                schedule_date: weekDates[dateIndex],
                start_time: startTime,
                end_time: endTime,
                employee: this.employees.find(
                    (employee) => employee.id === employeeId,
                ),
            }),
        );
        this.nextId = this.schedules.length + 1;
    }

    async getSchedules(filters = {}) {
        return {
            data: filterSchedules(this.schedules, filters),
            source: "local-fixture",
        };
    }

    async getScheduleById(id) {
        const schedule = this.schedules.find(
            (item) => String(item.id) === String(id),
        );

        if (!schedule)
            throw new Error("Jadwal tidak ditemukan pada data pratinjau.");

        return { data: schedule, source: "local-fixture" };
    }

    async createSchedule(payload) {
        const employee = this.activeEmployee(payload.employee_id);
        const schedule = {
            id: this.nextId++,
            ...payload,
            employee_id: employee.id,
            employee,
        };
        this.schedules = [...this.schedules, schedule];

        return { data: schedule, source: "local-fixture" };
    }

    async updateSchedule(id, payload) {
        const index = this.schedules.findIndex(
            (item) => String(item.id) === String(id),
        );

        if (index < 0)
            throw new Error("Jadwal tidak ditemukan pada data pratinjau.");

        const employee = this.activeEmployee(payload.employee_id);
        const schedule = {
            ...this.schedules[index],
            ...payload,
            employee_id: employee.id,
            employee,
        };
        this.schedules = this.schedules.map((item, itemIndex) =>
            itemIndex === index ? schedule : item,
        );

        return { data: schedule, source: "local-fixture" };
    }

    async deleteSchedule(id) {
        const exists = this.schedules.some(
            (item) => String(item.id) === String(id),
        );

        if (!exists)
            throw new Error("Jadwal tidak ditemukan pada data pratinjau.");

        this.schedules = this.schedules.filter(
            (item) => String(item.id) !== String(id),
        );

        return { data: { id }, source: "local-fixture" };
    }

    async getEmployeesForSchedule(filters = {}) {
        const employees =
            filters.is_active === true || filters.is_active === "1"
                ? this.employees.filter((employee) => employee.is_active)
                : this.employees;

        return { data: [...employees], source: "local-fixture" };
    }

    activeEmployee(id) {
        const employee = this.employees.find(
            (item) => String(item.id) === String(id),
        );

        if (!employee?.is_active)
            throw new Error("Pilih karyawan aktif dari data pratinjau.");

        return employee;
    }
}

export function toDateString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
}

export function getWeekRange(anchorDate) {
    const start = new Date(
        anchorDate.getFullYear(),
        anchorDate.getMonth(),
        anchorDate.getDate(),
    );
    const dayFromMonday = (start.getDay() + 6) % 7;
    start.setDate(start.getDate() - dayFromMonday);

    const end = new Date(start);
    end.setDate(start.getDate() + 6);

    return { from: toDateString(start), to: toDateString(end) };
}

export function parseDateString(value) {
    const [year, month, day] = String(value).split("-").map(Number);

    if (!year || !month || !day) return null;

    const date = new Date(year, month - 1, day);

    return toDateString(date) === value ? date : null;
}

export function getWeekDates(from) {
    const start = parseDateString(from);

    if (!start) return [];

    return Array.from({ length: 7 }, (_, index) => {
        const date = new Date(start);
        date.setDate(start.getDate() + index);

        return toDateString(date);
    });
}

export function normalizePosition(position) {
    const normalized = String(position ?? "")
        .trim()
        .toLocaleLowerCase("id-ID");

    if (normalized === "barista") return "barista";
    if (["kasir", "cashier"].includes(normalized)) return "cashier";
    if (["kitchen", "dapur"].includes(normalized)) return "kitchen";

    return normalized;
}

export function filterSchedules(schedules, filters = {}) {
    const query = String(filters.q ?? filters.search ?? "")
        .trim()
        .toLocaleLowerCase("id-ID");
    const position = String(filters.position ?? "").trim();

    return schedules.filter((schedule) => {
        if (filters.from && schedule.schedule_date < filters.from) return false;
        if (filters.to && schedule.schedule_date > filters.to) return false;
        if (
            position &&
            normalizePosition(schedule.employee?.position) !==
                normalizePosition(position)
        )
            return false;
        if (
            filters.employee_id &&
            String(schedule.employee_id) !== String(filters.employee_id)
        )
            return false;

        if (!query) return true;

        const employeeName = String(
            schedule.employee?.full_name ?? "",
        ).toLocaleLowerCase("id-ID");
        const employeeCode = String(
            schedule.employee?.employee_code ?? "",
        ).toLocaleLowerCase("id-ID");

        return employeeName.includes(query) || employeeCode.includes(query);
    });
}

export function summarizeSchedules(schedules) {
    const employeeIds = new Set(
        schedules.map((schedule) => String(schedule.employee_id)),
    );
    const assignments = { barista: 0, cashier: 0, kitchen: 0 };

    for (const schedule of schedules) {
        const position = normalizePosition(schedule.employee?.position);

        if (Object.hasOwn(assignments, position)) assignments[position] += 1;
    }

    return {
        employees: employeeIds.size,
        assignments: schedules.length,
        barista: assignments.barista,
        cashier: assignments.cashier,
        kitchen: assignments.kitchen,
    };
}

export function validateSchedule(
    payload,
    employees,
    { allowInactiveEmployeeId = null } = {},
) {
    const errors = {};
    const employee = employees.find(
        (item) => String(item.id) === String(payload.employee_id),
    );

    if (
        !employee ||
        (!employee.is_active &&
            String(employee.id) !== String(allowInactiveEmployeeId))
    ) {
        errors.employee_id = "Pilih karyawan aktif.";
    }
    if (!String(payload.shift_name ?? "").trim())
        errors.shift_name = "Nama shift wajib diisi.";
    if (String(payload.shift_name ?? "").trim().length > 50)
        errors.shift_name = "Nama shift maksimal 50 karakter.";
    if (!parseDateString(payload.schedule_date))
        errors.schedule_date = "Tanggal jadwal tidak valid.";
    if (!/^([01]\d|2[0-3]):[0-5]\d$/.test(String(payload.start_time ?? "")))
        errors.start_time = "Jam mulai tidak valid.";
    if (!/^([01]\d|2[0-3]):[0-5]\d$/.test(String(payload.end_time ?? "")))
        errors.end_time = "Jam selesai tidak valid.";
    if (
        payload.start_time &&
        payload.end_time &&
        payload.end_time <= payload.start_time
    ) {
        errors.end_time = "Jam selesai harus setelah jam mulai.";
    }

    return errors;
}

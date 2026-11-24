import { apiFetch } from "../utils/api.js";
import { parseDateString } from "./logic.js";
import { FixtureAdapter } from "./mock-adapter.js";

export const SCHEDULE_ENDPOINTS = {
    schedules: "/api/employee-schedules",
    employees: "/api/employees",
};

function appendFilters(url, filters = {}) {
    const query = new URLSearchParams();

    for (const key of [
        "from",
        "to",
        "position",
        "employee_id",
        "is_active",
        "q",
    ]) {
        if (
            filters[key] !== undefined &&
            filters[key] !== null &&
            filters[key] !== ""
        ) {
            query.set(key, filters[key]);
        }
    }

    const search = query.toString();

    return search ? `${url}?${search}` : url;
}

async function requireOk(url, options) {
    const result = await apiFetch(url, options);

    if (!result.response.ok) {
        const error = new Error(
            result.data?.message || "Data jadwal tidak dapat diproses.",
        );
        error.status = result.response.status;
        error.data = result.data;

        throw error;
    }

    return result.data;
}

export class ApiAdapter {
    async getSchedules(filters) {
        return requireOk(appendFilters(SCHEDULE_ENDPOINTS.schedules, filters));
    }

    async getScheduleById(id) {
        return requireOk(
            `${SCHEDULE_ENDPOINTS.schedules}/${encodeURIComponent(id)}`,
        );
    }

    async createSchedule(payload) {
        return requireOk(SCHEDULE_ENDPOINTS.schedules, {
            method: "POST",
            body: payload,
        });
    }

    async updateSchedule(id, payload) {
        return requireOk(
            `${SCHEDULE_ENDPOINTS.schedules}/${encodeURIComponent(id)}`,
            {
                method: "PUT",
                body: payload,
            },
        );
    }

    async deleteSchedule(id) {
        return requireOk(
            `${SCHEDULE_ENDPOINTS.schedules}/${encodeURIComponent(id)}`,
            {
                method: "DELETE",
            },
        );
    }

    async getEmployeesForSchedule(filters = {}) {
        return requireOk(appendFilters(SCHEDULE_ENDPOINTS.employees, filters));
    }
}

const serverAnchor =
    document.querySelector("#schedule-app")?.dataset.currentDate;
const fixtureAnchor = parseDateString(serverAnchor) ?? new Date();
const adapter = new FixtureAdapter(fixtureAnchor);

export const employeeScheduleService = {
    getSchedules: (filters) => adapter.getSchedules(filters),
    getScheduleById: (id) => adapter.getScheduleById(id),
    createSchedule: (payload) => adapter.createSchedule(payload),
    updateSchedule: (id, payload) => adapter.updateSchedule(id, payload),
    deleteSchedule: (id) => adapter.deleteSchedule(id),
    getEmployeesForSchedule: (filters) =>
        adapter.getEmployeesForSchedule(filters),
};

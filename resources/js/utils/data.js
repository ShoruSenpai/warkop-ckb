export function findById(items, id) {
    return items.find((item) => Number(item.id) === Number(id));
}

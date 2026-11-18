<?php
/** Shared GET search, exact-match filters and database pagination. */
function listing_identifier(string $name): string
{
    // SQL identifiers must come from developer configuration, never from GET.
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $name)) {
        throw new InvalidArgumentException('Invalid database identifier.');
    }
    return '`' . $name . '`';
}

/** Each filter: ['column'=>..., 'options'=>['allowed value'=>'description']]. */
function listing_state(array $query, array $filters = [], int $perPage = 10): array
{
    $q = $query['q'] ?? '';
    $q = is_string($q) && preg_match('//u', $q) ? mb_substr(trim($q), 0, 200) : '';
    $page = $query['page'] ?? '1';
    $page = is_scalar($page) ? filter_var($page, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]) : false;
    $selected = [];
    foreach ($filters as $name => $definition) {
        if (in_array($name, ['q', 'page'], true)) throw new InvalidArgumentException('Reserved filter name.');
        $value = $query[$name] ?? '';
        if (is_string($value) && $value !== '' && array_key_exists($value, $definition['options'])) {
            $selected[$name] = $value;
        }
    }
    return ['q'=>$q, 'filters'=>$selected, 'page'=>$page ?: 1, 'per_page'=>max(1, min(100, $perPage))];
}

function listing_pagination(int $total, int $page, int $perPage): array
{
    $total = max(0, $total);
    $perPage = max(1, min(100, $perPage));
    $pages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($pages, $page));
    return ['total'=>$total, 'page'=>$page, 'pages'=>$pages, 'per_page'=>$perPage,
        'offset'=>($page - 1) * $perPage];
}

/**
 * MySQL/MariaDB. Table, columns, filters and sort are trusted developer config.
 * Pass an explicit SELECT column list (avoid loading CV BLOBs in EOI listings).
 * Sort must include a unique key, e.g. ['EOInumber'=>'DESC'], for stable pages.
 * Authorize protected pages BEFORE calling this function; it does not handle login.
 */
function listing_query(PDO $db, string $table, array $columns, array $searchColumns,
    array $filterDefinitions, array $state, array $sort): array
{
    if (!$columns || !$sort) throw new InvalidArgumentException('Columns and stable sort are required.');
    $table = listing_identifier($table);
    $select = implode(', ', array_map('listing_identifier', $columns));
    $where = [];
    $params = [];
    if ($state['q'] !== '' && $searchColumns) {
        $search = [];
        // ! escapes SQL LIKE wildcards so % and _ are searched literally.
        $term = '%' . strtr($state['q'], ['!'=>'!!', '%'=>'!%', '_'=>'!_']) . '%';
        foreach ($searchColumns as $column) {
            $search[] = listing_identifier($column) . " LIKE ? ESCAPE '!'";
            $params[] = $term;
        }
        $where[] = '(' . implode(' OR ', $search) . ')';
    }
    foreach ($state['filters'] as $name => $value) {
        if (!isset($filterDefinitions[$name]) || !array_key_exists($value, $filterDefinitions[$name]['options'])) {
            throw new InvalidArgumentException('Unknown filter value.');
        }
        $where[] = listing_identifier($filterDefinitions[$name]['column']) . ' = ?';
        $params[] = $value;
    }
    $order = [];
    foreach ($sort as $column => $direction) {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) throw new InvalidArgumentException('Invalid sort direction.');
        $order[] = listing_identifier($column) . ' ' . $direction;
    }
    $clause = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $count = $db->prepare("SELECT COUNT(*) FROM $table$clause");
    $count->execute($params);
    $pagination = listing_pagination((int) $count->fetchColumn(), $state['page'], $state['per_page']);
    $sql = "SELECT $select FROM $table$clause ORDER BY " . implode(', ', $order) . ' LIMIT ? OFFSET ?';
    $statement = $db->prepare($sql);
    foreach ($params as $i => $value) $statement->bindValue($i + 1, $value, PDO::PARAM_STR);
    $statement->bindValue(count($params) + 1, $pagination['per_page'], PDO::PARAM_INT);
    $statement->bindValue(count($params) + 2, $pagination['offset'], PDO::PARAM_INT);
    $statement->execute();
    return ['rows'=>$statement->fetchAll(PDO::FETCH_ASSOC), 'pagination'=>$pagination];
}

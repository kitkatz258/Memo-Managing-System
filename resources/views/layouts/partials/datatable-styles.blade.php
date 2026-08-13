<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    .memo-pill {
        cursor: pointer;
        transition: .2s;
    }
    .memo-pill:hover {
        transform: translateY(-1px);
        background: #635BFF;
        color: white;
    }

    .view-pdf-btn,
    .view-pdf-btn:hover {
        cursor: pointer;
    }

    table.dataTable thead th {
        font-weight: 600;
        font-size: 0.8125rem;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
        padding: 1.1rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .dark table.dataTable thead th {
        color: #cbd5e1;
        border-bottom-color: #475569;
    }

    table.dataTable tbody td {
        vertical-align: middle;
        line-height: 1.35;
        text-align: center;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .dark table.dataTable tbody td {
        color: #f1f5f9;
        border-bottom-color: #334155;
    }

    table.dataTable tbody tr {
        transition: .2s;
    }
    table.dataTable tbody tr:hover {
        background: #fafafa;
    }
    .dark table.dataTable tbody tr:hover { background-color: #1e293b; }
    table.dataTable tbody tr:last-child td { border-bottom: none; }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-bottom: 1rem;
        color: #64748b;
        font-size: 0.875rem;
    }
    .dark .dataTables_wrapper .dataTables_length,
    .dark .dataTables_wrapper .dataTables_filter,
    .dark .dataTables_wrapper .dataTables_info,
    .dark .dataTables_wrapper .dataTables_paginate {
        color: #94a3b8;
    }

    .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        margin-left: 0.5rem;
        background-color: white;
    }
    .dark .dataTables_filter input {
        background-color: #1e293b;
        color: white;
        border-color: #334155;
    }

    .dataTables_length select {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.375rem !important;
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        margin: 0 0.4rem !important;
        background-color: white !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3e%3cpath d='M6 9l6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.5rem center !important;
        background-size: 1rem !important;
        min-width: 4.5rem;
    }
    .dark .dataTables_length select {
        background-color: #1e293b;
        color: white;
        border-color: #334155;
    }

    .dataTables_wrapper .dataTables_paginate {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .dataTables_paginate .paginate_button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.6rem;
        border-radius: 9999px !important;
        border: 1px solid transparent !important;
        background: transparent !important;
        cursor: pointer;
        font-size: 0.875rem;
    }
    .paginate_button:not(.disabled):not(.current):hover {
        background: #f1f5f9 !important;
        color: #1e293b !important;
        border: 1px solid #e2e8f0 !important;
    }
    .dark .paginate_button:not(.disabled):not(.current):hover {
        background: #1e293b !important;
        color: white !important;
        border-color: #334155 !important;
    }
    .dataTables_paginate .paginate_button.current {
        background: #635BFF !important;
        color: white !important;
        border: none !important;
        font-weight: 600;
    }
    .dataTables_paginate .paginate_button.disabled {
        opacity: 0.35;
        cursor: default;
    }
    .dataTables_paginate .ellipsis {
        padding: 0 0.25rem;
        color: #94a3b8;
    }
</style>
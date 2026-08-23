<style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Hiragino Kaku Gothic ProN", "Yu Gothic", sans-serif;
        max-width: 720px;
        margin: 40px auto;
        padding: 0 16px;
        color: #222;
    }
    h1 {
        font-size: 1.5rem;
        margin-bottom: 24px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px 12px;
        text-align: left;
        vertical-align: top;
    }
    th {
        background: #f5f5f5;
        width: 8em;
    }
    dl.review dt {
        font-weight: bold;
        margin-top: 12px;
    }
    dl.review dd {
        margin: 4px 0 0 0;
        white-space: pre-wrap;
    }
    .form-row {
        margin-bottom: 16px;
    }
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 4px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea,
    select {
        width: 100%;
        box-sizing: border-box;
        padding: 8px;
        font-size: 1rem;
    }
    .error {
        color: #c0392b;
        font-size: 0.9rem;
        margin-top: 4px;
    }
    .flash {
        background: #eafaf1;
        border: 1px solid #2ecc71;
        padding: 8px 12px;
        margin-bottom: 16px;
    }
    .buttons {
        margin-top: 24px;
    }
    button, .button-link {
        display: inline-block;
        padding: 8px 20px;
        font-size: 1rem;
        cursor: pointer;
    }
    nav {
        margin-bottom: 24px;
        font-size: 0.9rem;
    }
    nav a {
        margin-right: 12px;
    }
    nav form {
        display: inline;
    }
    nav button {
        padding: 0;
        border: none;
        background: none;
        font-size: inherit;
        color: #0563c1;
        text-decoration: underline;
    }
    .pagination {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 16px 0;
        padding: 0;
        gap: 4px;
    }
    .page-item .page-link {
        display: inline-block;
        padding: 6px 12px;
        border: 1px solid #ddd;
        color: #0563c1;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .page-item.active .page-link {
        background: #f5f5f5;
        color: #222;
        font-weight: bold;
    }
    .page-item.disabled .page-link {
        color: #999;
        cursor: default;
    }
</style>

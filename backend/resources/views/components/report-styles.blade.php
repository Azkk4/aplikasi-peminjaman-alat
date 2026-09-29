<style>
    .print-report-document { display: none; color-scheme: light; color: #17212b; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.45; }
    .report-sheet { width: min(1120px, 100%); margin: 32px auto; padding: 38px 42px; background: #fff; box-shadow: 0 8px 30px rgb(15 23 42 / 10%); }
    .report-masthead { display: flex; justify-content: space-between; gap: 24px; align-items: flex-start; padding-bottom: 18px; border-bottom: 2px solid #1f4b5a; }
    .report-organization { margin: 0; color: #1f4b5a; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .report-title { margin: 8px 0 0; font-size: 23px; line-height: 1.2; }
    .report-generated { margin: 4px 0 0; color: #52616b; text-align: right; white-space: nowrap; }
    .report-meta { display: flex; flex-wrap: wrap; gap: 8px 28px; padding: 16px 0 20px; color: #374650; }
    .report-meta strong { color: #17212b; }
    .report-table-wrap { width: 100%; overflow-x: auto; }
    .report-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .report-table th, .report-table td { padding: 9px 10px; border: 1px solid #cbd5da; text-align: left; vertical-align: top; overflow-wrap: anywhere; }
    .report-table th { background: #eaf0f2; color: #203a45; font-size: 10px; letter-spacing: .04em; text-transform: uppercase; }
    .report-table .column-number { width: 42px; text-align: center; }
    .report-table .column-name { width: 19%; }
    .report-table .column-date { width: 15%; }
    .report-table .column-tools { width: 31%; }
    .report-table .column-status { width: 13%; }
    .tool-list { margin: 0; padding-left: 17px; }
    .tool-list li + li { margin-top: 3px; }
    .status-label { font-weight: 700; }
    .report-empty { padding: 24px !important; color: #52616b; text-align: center !important; }
    .report-total { margin: 12px 0 0; color: #52616b; text-align: right; }
    @media (max-width: 700px) {
        .report-sheet { margin: 10px auto; padding: 22px 16px; }
        .report-masthead { flex-direction: column; }
        .report-generated { text-align: left; }
        .report-table { min-width: 760px; }
    }
    @page { size: A4 landscape; margin: 12mm; }
    @media print {
        body.report-page { display: block !important; background: #fff !important; color: #111; font-size: 10pt; }
        body.report-page #app-shell { display: block !important; min-height: 0 !important; overflow: visible !important; }
        body.report-page #app-content { display: block !important; overflow: visible !important; margin-left: 0 !important; }
        body.report-page #app-main-region { min-height: 0 !important; padding-top: 0 !important; }
        body.report-page main { position: static !important; display: block !important; padding: 0 !important; }
        body.report-page .report-screen { display: none !important; }
        body.report-page .print-report-document { display: block !important; }
        body.report-page .print-hidden,
        body.report-page #global-notification,
        body.report-page #page-loading-skeleton,
        body.report-page #confirmation-modal,
        body.report-page #image-crop-modal { display: none !important; }
        .report-sheet { width: 100%; margin: 0; padding: 0; box-shadow: none; }
        .report-masthead { padding-bottom: 10px; }
        .report-title { font-size: 18pt; }
        .report-meta { padding: 10px 0 14px; }
        .report-table-wrap { overflow: visible; }
        .report-table { min-width: 0; font-size: 9pt; }
        .report-table thead { display: table-header-group; }
        .report-table tr { break-inside: avoid; }
        .report-table th, .report-table td { padding: 6px 7px; border-color: #8b969b; }
        .report-table th { background: #eaf0f2 !important; print-color-adjust: exact; }
        a { color: inherit; text-decoration: none; }
    }
</style>
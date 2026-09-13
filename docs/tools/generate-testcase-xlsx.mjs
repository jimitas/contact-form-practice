// docs/テストケース.md を読み取り、手動確認用のチェックシート(.xlsx)を生成する。
//
// 使い方（プロジェクトルートで実行）:
//   npm install exceljs        # 未インストールの場合のみ
//   node docs/tools/generate-testcase-xlsx.mjs
//
// テストケースの正は常に docs/テストケース.md 側。
// ケースを追加・変更したら .md を直してから再生成すること（xlsx を直接編集しない）。
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import ExcelJS from 'exceljs';

const DOCS = path.resolve(fileURLToPath(import.meta.url), '../..');
const SRC = path.join(DOCS, 'テストケース.md');
const OUT = path.join(DOCS, 'テストケース_チェックシート.xlsx');

// ---- マークダウンの表をパースする ----
const lines = fs.readFileSync(SRC, 'utf8').split(/\r?\n/);

/** セル内のマークダウン記法（バックティック）を落とす */
const clean = (s) => s.replace(/`/g, '').trim();

const rows = [];
let major = '';
let minor = '';

for (const line of lines) {
  if (line.startsWith('## ')) {
    major = clean(line.slice(3));
    minor = '';
    continue;
  }
  if (line.startsWith('### ')) {
    minor = clean(line.slice(4));
    continue;
  }
  if (!line.startsWith('|')) continue;

  const cells = line.split('|').slice(1, -1).map(clean);
  if (cells.length < 4) continue;
  if (cells[0] === 'No.') continue;          // ヘッダ行
  if (/^-+$/.test(cells[0])) continue;       // 区切り行

  rows.push({
    no: cells[0],
    major,
    minor,
    content: cells[1],
    steps: cells[2],
    expected: cells[3],
  });
}

if (rows.length === 0) throw new Error('テストケースを1件も抽出できませんでした');

// ---- ワークブックを組み立てる ----
const wb = new ExcelJS.Workbook();
wb.creator = 'contact-form';
wb.created = new Date();

const HEADERS = [
  { key: 'no', label: 'No.', width: 10 },
  { key: 'major', label: '大項目', width: 28 },
  { key: 'minor', label: '中項目', width: 30 },
  { key: 'content', label: 'テスト内容', width: 28 },
  { key: 'steps', label: '前提条件・手順', width: 44 },
  { key: 'expected', label: '期待結果', width: 52 },
  { key: 'result', label: '結果', width: 8 },
  { key: 'date', label: '確認日', width: 12 },
  { key: 'tester', label: '確認者', width: 12 },
  { key: 'note', label: '備考', width: 34 },
];

const ws = wb.addWorksheet('テストケース', {
  views: [{ state: 'frozen', xSplit: 1, ySplit: 1 }],
  pageSetup: { orientation: 'landscape', fitToPage: true, fitToWidth: 1, fitToHeight: 0 },
});

ws.columns = HEADERS.map((h) => ({ header: h.label, key: h.key, width: h.width }));

const header = ws.getRow(1);
header.height = 24;
header.eachCell((cell) => {
  cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
  cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF44546A' } };
  cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
  cell.border = {
    top: { style: 'thin', color: { argb: 'FFBFBFBF' } },
    left: { style: 'thin', color: { argb: 'FFBFBFBF' } },
    bottom: { style: 'thin', color: { argb: 'FFBFBFBF' } },
    right: { style: 'thin', color: { argb: 'FFBFBFBF' } },
  };
});

for (const r of rows) {
  ws.addRow({
    no: r.no,
    major: r.major,
    minor: r.minor,
    content: r.content,
    steps: r.steps,
    expected: r.expected,
    result: '',
    date: '',
    tester: '',
    note: '',
  });
}

const lastRow = ws.rowCount;

// 本文セルの体裁
for (let i = 2; i <= lastRow; i++) {
  const row = ws.getRow(i);
  row.eachCell({ includeEmpty: true }, (cell, col) => {
    cell.alignment = {
      vertical: 'top',
      wrapText: true,
      horizontal: col === 1 || col === 7 || col === 8 ? 'center' : 'left',
    };
    cell.border = {
      top: { style: 'hair', color: { argb: 'FFD0D0D0' } },
      left: { style: 'hair', color: { argb: 'FFD0D0D0' } },
      bottom: { style: 'hair', color: { argb: 'FFD0D0D0' } },
      right: { style: 'hair', color: { argb: 'FFD0D0D0' } },
    };
  });
  // 「結果」欄は○×を大きめに見せる
  row.getCell('result').font = { size: 14, bold: true };
  row.getCell('date').numFmt = 'yyyy/mm/dd';
}

// 「結果」欄に ○ / × / － のドロップダウンを付ける
for (let i = 2; i <= lastRow; i++) {
  ws.getCell(`G${i}`).dataValidation = {
    type: 'list',
    allowBlank: true,
    formulae: ['"○,×,－"'],
    showErrorMessage: true,
    errorStyle: 'warning',
    errorTitle: '入力値が不正です',
    error: '○（合格） / ×（不合格） / －（対象外・未実施）から選んでください。',
  };
}

// ○×に応じて行に色を付ける
ws.addConditionalFormatting({
  ref: `A2:J${lastRow}`,
  rules: [
    {
      type: 'expression',
      priority: 1,
      formulae: ['$G2="×"'],
      style: { fill: { type: 'pattern', pattern: 'solid', bgColor: { argb: 'FFFCE4E4' } } },
    },
    {
      type: 'expression',
      priority: 2,
      formulae: ['$G2="○"'],
      style: { fill: { type: 'pattern', pattern: 'solid', bgColor: { argb: 'FFE9F5EA' } } },
    },
    {
      type: 'expression',
      priority: 3,
      formulae: ['$G2="－"'],
      style: { fill: { type: 'pattern', pattern: 'solid', bgColor: { argb: 'FFF0F0F0' } } },
    },
  ],
});

ws.autoFilter = { from: 'A1', to: `J${lastRow}` };

// ---- 集計シート ----
const sum = wb.addWorksheet('集計', { views: [{ state: 'frozen', ySplit: 1 }] });
sum.columns = [
  { header: '項目', key: 'k', width: 46 },
  { header: '件数', key: 'v', width: 12 },
];
const sh = sum.getRow(1);
sh.font = { bold: true, color: { argb: 'FFFFFFFF' } };
sh.eachCell((c) => {
  c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF44546A' } };
  c.alignment = { horizontal: 'center', vertical: 'middle' };
});

const R = `'テストケース'!$G$2:$G$${lastRow}`;
const overall = [
  ['総ケース数', { formula: `COUNTA('テストケース'!$A$2:$A$${lastRow})` }],
  ['○（合格）', { formula: `COUNTIF(${R},"○")` }],
  ['×（不合格）', { formula: `COUNTIF(${R},"×")` }],
  ['－（対象外・未実施）', { formula: `COUNTIF(${R},"－")` }],
  ['未確認（空欄）', { formula: `B2-B3-B4-B5` }],
];
overall.forEach(([k, v]) => sum.addRow({ k, v }));
sum.addRow({ k: '消化率', v: { formula: `IF(B2=0,0,(B3+B4+B5)/B2)` } });
sum.getCell(`B${sum.rowCount}`).numFmt = '0.0%';

sum.addRow({});
const secHeadRow = sum.addRow({ k: '中項目別', v: '○ / 総数' });
secHeadRow.font = { bold: true };
secHeadRow.eachCell((c) => {
  c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFDCE6F1' } };
});

const minors = [...new Set(rows.map((r) => r.minor))];
for (const m of minors) {
  const total = rows.filter((r) => r.minor === m).length;
  const row = sum.addRow({
    k: m,
    v: {
      formula: `COUNTIFS('テストケース'!$C$2:$C$${lastRow},$A${sum.rowCount + 1},${R},"○")&" / ${total}"`,
    },
  });
  row.getCell('v').alignment = { horizontal: 'center' };
}

sum.eachRow((row, i) => {
  if (i === 1) return;
  row.getCell('k').alignment = { vertical: 'middle' };
});

// ---- 凡例シート ----
const legend = wb.addWorksheet('凡例');
legend.columns = [
  { header: '記号', key: 'a', width: 10 },
  { header: '意味', key: 'b', width: 60 },
];
const lh = legend.getRow(1);
lh.font = { bold: true, color: { argb: 'FFFFFFFF' } };
lh.eachCell((c) => {
  c.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF44546A' } };
  c.alignment = { horizontal: 'center', vertical: 'middle' };
});
[
  ['○', '期待結果のとおりだった（合格）'],
  ['×', '期待結果と異なった（不合格）。内容は「備考」欄に記入する'],
  ['－', '対象外、または今回は確認しない'],
  ['（空欄）', 'まだ確認していない'],
].forEach(([a, b]) => legend.addRow({ a, b }));
legend.getColumn('a').alignment = { horizontal: 'center' };
legend.addRow({});
legend.addRow({ a: '', b: 'このシートは docs/テストケース.md から自動生成しています。' });
legend.addRow({ a: '', b: 'テストケースを追加・変更するときは、まず .md を直してから再生成してください。' });

await wb.xlsx.writeFile(OUT);
console.log(`OK: ${rows.length}件のテストケースを書き出しました -> ${OUT}`);
console.log(`中項目: ${minors.length}区分`);


    // ============================================================
    //  HIGHCHARTS GLOBAL DEFAULTS
    // ============================================================
    Highcharts.setOptions({
    chart: { style: { fontFamily: "'Plus Jakarta Sans',sans-serif" } },
    credits: { enabled: false },
    title: { text: '' },
    tooltip: { borderRadius: 10, shadow: false }
});

    var tatTrendChartInst = null;
    var tatDailyChartInst = null;

    function collNav(e){
    const home=document.getElementById("home");//style="width: 100%;"
    const nav=document.getElementById("hide_nav_");
    nav.classList.toggle('hide_nav_');
    home.classList.toggle("full-width");
    setTimeout(function () {
    Highcharts.charts.forEach(function (chart) {
    if (chart) chart.reflow();
});
}, 420);
}

    // ============================================================
    //  Multi-line TAT Trend Chart
    //  JSON: line_chart.x_axis = string[]
    //        line_chart.series = [{name, color, data[]}, ...]
    // ============================================================
    function drawTrendChart(xAxis, seriesArr) {
    if (tatTrendChartInst) tatTrendChartInst.destroy();

    var series = seriesArr.map(function (s) {
    return {
    name:  s.name,
    color: s.color,
    data:  s.data.map(function (v) {
    var n = parseFloat(v);
    return isNaN(n) ? 0 : n;
})
};
});

    tatTrendChartInst = Highcharts.chart('tatTrendChart', {
    chart: {
    type: 'line',
    backgroundColor: 'transparent',
    margin: [60, 20, 60, 55]  // ✅ top margin badha diya legend ke liye
},
    xAxis: {
    categories: xAxis,
    lineColor: '#e8ecf4',
    tickColor: '#e8ecf4',
    labels: {
    style: { fontSize: '11px', color: '#8892aa', fontWeight: '600' }
}
},
    yAxis: {
    title: {
    text: 'AVG TAT',
    style: { fontSize: '10px', color: '#8892aa' }
},
    labels: {
    style: { fontSize: '10px', color: '#8892aa' }
},
    gridLineColor: '#f0f2f8',
    min: 0
},
    tooltip: {
    shared: true,
    borderRadius: 10,
    shadow: false,
    backgroundColor: '#ffffff',
    borderColor: '#e8ecf4',
    style: { fontSize: '12px', color: '#0f1629' },
    // ── FIX: TAT-1 hours mein hai, TAT-2/3 days mein
    formatter: function () {
    var s = '<b>' + this.x + '</b><br/>';
    this.points.forEach(function (p) {
    var unit = p.series.name.indexOf('hrs') !== -1 ? ' days' : ' days';
    s += '<span style="color:' + p.color + '">●</span> '
    + p.series.name + ': <b>'
    + (p.y !== null ? p.y.toFixed(2) + unit : '—')
    + '</b><br/>';
});
    return s;
}
},
    legend: {
    enabled: true,
    align: 'center',
    verticalAlign: 'top',      // ✅ bottom se top
    itemStyle: { fontSize: '11px', fontWeight: '600', color: '#3d4966' }
},
    plotOptions: {
    line: {
    lineWidth: 2.5,
    connectNulls: false,   // null pe line break hogi — sahi behaviour
    marker: {
    radius: 4,
    symbol: 'circle',
    lineWidth: 2,
    lineColor: '#fff'
}
}
},
    series: series
});
}

    // ============================================================
    //  setAllData — master setter
    //  JSON keys:
    //    card_data, line_chart,
    //    call_by_tat_1, call_by_tat_2, call_by_tat_3,
    //    avg_tat_by_product, zone_wise_tat
    // ============================================================
    TatDocmentManager.prototype.setAllData = function (data) {
    if (data.card_data)          this.setCardData(data.card_data);

    // ── FIX: line_chart ke andar x_axis aur series hain
    if (data.line_chart && data.line_chart.x_axis && data.line_chart.series) {
    drawTrendChart(data.line_chart.x_axis, data.line_chart.series);
}

    if (data.call_by_tat_1)      this.setBucketDataById_tat1('tatBucketWrap',   data.call_by_tat_1);
    if (data.call_by_tat_2)      this.setBucketDataById_tat2('tatBucketWrap-2', data.call_by_tat_2);
    if (data.call_by_tat_3)      this.setBucketDataById_tat3('tatBucketWrap-3', data.call_by_tat_3);
    if (data.avg_tat_by_product) this.setProductTable(data.avg_tat_by_product);
    if (data.zone_wise_tat)      this.setZoneData(data.zone_wise_tat);
};

    // ============================================================
    //  TatDocmentManager
    // ============================================================
    function TatDocmentManager() {
    this.dashboardpage     = null;
    this.total_job_card    = null;
    this.avg_tat_card      = null;
    this.within_tat_card   = null;
    this.sla_breached_card = null;
    this.at_risk_card      = null;
    this.tat_table         = null;
    this.tat_zone_list     = null;
    this.dataRange         = null;
    this.zone              = null;
    this.state             = null;
    this.bsi               = null;
    this.enginertype       = null;
    this.product           = null;
    this.submit_button     = null;
    this.loader            = null;
}

    TatDocmentManager.prototype.init = function () {
    this.dashboardpage     = document.getElementById('dashboard_home');
    this.total_job_card    = document.getElementById('total_jobs');
    this.avg_tat_card      = document.getElementById('avg_tat_card');
    this.within_tat_card   = document.getElementById('within_tat_card');
    this.sla_breached_card = document.getElementById('sla_breached_card');
    this.at_risk_card      = document.getElementById('at_risk_card');
    this.tat_table         = document.getElementById('tat_table');
    this.tat_zone_list     = document.getElementById('tat-zone-list');
    this.dataRange         = document.getElementById('date_rng');
    this.zone              = document.querySelector('[name="zone"]');
    this.state             = document.querySelector('[name="state"]');
    this.bsi               = document.querySelector('[name="bsi"]');
    this.enginertype       = document.querySelector('[name="enginer_type"]');
    this.product           = document.querySelector('[name="product"]');
    this.submit_button     = document.getElementById('submit_button');
    this.loader            = document.getElementById('dashboardLoader');
};

    TatDocmentManager.prototype.showLoader    = function () { this.loader.classList.add('active');     this.hideDashboard(); };
    TatDocmentManager.prototype.hideLoader    = function () { this.loader.classList.remove('active');  };
    TatDocmentManager.prototype.showDashboard = function () { this.dashboardpage.classList.remove('hidden'); };
    TatDocmentManager.prototype.hideDashboard = function () { this.dashboardpage.classList.add('hidden');    };

    TatDocmentManager.prototype.bindEvent = function () {
    var self = this;
    document.getElementById('submit_button').addEventListener('click', function (e) {
    e.preventDefault();
    self.formSubmit();
});
    document.getElementById('reset_button').addEventListener('click', function () {
    document.getElementById('dashboard_form').reset();
});
};

    TatDocmentManager.prototype.showMessage = function (message, type) {
    type = type || 'error';
    var bgColor = type === 'success' ? '#22c55e'
    : type === 'warning'         ? '#f59e0b'
    :                              '#ef4444';
    var div = document.createElement('div');
    div.className = 'custom-alert';
    div.style.background = bgColor;
    div.innerText = message;
    document.body.appendChild(div);
    setTimeout(function () { div.classList.add('show'); }, 100);
    setTimeout(function () {
    div.classList.remove('show');
    setTimeout(function () { div.remove(); }, 300);
}, 3500);
};

    // ============================================================
    //  setCardData
    //  JSON keys: total_jobs, avg_tat, tat_1[0/1], tat2[0/1], tat_3[0/1]
    // ============================================================
    TatDocmentManager.prototype.setCardData = function (card_data) {
    // Total Jobs
    this.total_job_card.querySelector('.tat-kpi-value').textContent = card_data.total_jobs;

    // Avg TAT
    this.avg_tat_card.querySelector('.tat-kpi-value').textContent = card_data.avg_tat;

    // TAT-1  (key: tat_1)
    document.getElementById('tat1_val').textContent = card_data.tat_1[0];
    document.getElementById('tat1_pct').textContent = card_data.tat_1[1] + ' calls';
    document.getElementById('tat1_bar').style.width = Math.min(parseFloat(card_data.tat_1[1]) || 0, 100) + '%';

    // TAT-2  (key: tat2)
    document.getElementById('tat2_val').textContent = card_data.tat2[0];
    document.getElementById('tat2_pct').textContent = card_data.tat2[1] + ' of jobs';
    document.getElementById('tat2_bar').style.width = Math.min(parseFloat(card_data.tat2[1]) || 0, 100) + '%';

    // TAT-3  (key: tat_3)
    document.getElementById('tat3_val').textContent = card_data.tat_3[0];
    document.getElementById('tat3_pct').textContent = card_data.tat_3[1] + ' of jobs';
    document.getElementById('tat3_bar').style.width = Math.min(parseFloat(card_data.tat_3[1]) || 0, 100) + '%';
};

    // ============================================================
    //  setBucketDataById — generic, works for all 3 wrappers
    //  JSON bucket keys: 0_24, 24_48, 3_6, 6_10, 11_15, above_15
    // ============================================================
    TatDocmentManager.prototype.setBucketDataById_tat1 = function (wrapperId, bucket) {
    var keys = ['24', '36', '48', '72', '72_plus'];
    var wrap = document.getElementById(wrapperId);
    if (!wrap) return;

    wrap.querySelectorAll('.tat-bucket-row').forEach(function (row, i) {
    var fill  = row.querySelector('.tat-bucket-fill');
    var valEl = row.querySelector('.tat-bucket-val');
    var entry = bucket[keys[i]];

    var count  = (entry && entry[0] !== undefined) ? entry[0] : '0';
    var pctStr = (entry && entry[1] !== undefined) ? entry[1] : '0%';
    var pctNum = Math.min(parseFloat(pctStr) || 0, 100);

    fill.setAttribute('data-count', count);
    fill.setAttribute('data-pct',   pctStr);
    fill.style.width  = pctNum + '%';
    valEl.textContent = parseInt(count).toLocaleString();
    valEl.style.color = fill.getAttribute('data-color') || fill.style.background;
});
};
    TatDocmentManager.prototype.setBucketDataById_tat2 = function (wrapperId, bucket) {
    var keys = ['3', '5', '7', '7_plus'];
    var wrap = document.getElementById(wrapperId);
    if (!wrap) return;

    wrap.querySelectorAll('.tat-bucket-row').forEach(function (row, i) {
    var fill  = row.querySelector('.tat-bucket-fill');
    var valEl = row.querySelector('.tat-bucket-val');
    var entry = bucket[keys[i]];

    var count  = (entry && entry[0] !== undefined) ? entry[0] : '0';
    var pctStr = (entry && entry[1] !== undefined) ? entry[1] : '0%';
    var pctNum = Math.min(parseFloat(pctStr) || 0, 100);

    fill.setAttribute('data-count', count);
    fill.setAttribute('data-pct',   pctStr);
    fill.style.width  = pctNum + '%';
    valEl.textContent = parseInt(count).toLocaleString();
    valEl.style.color = fill.getAttribute('data-color') || fill.style.background;
});
};
    TatDocmentManager.prototype.setBucketDataById_tat3 = function (wrapperId, bucket) {
    var keys = ['7', '15', '21', '30', '30_plus'];
    var wrap = document.getElementById(wrapperId);
    if (!wrap) return;

    wrap.querySelectorAll('.tat-bucket-row').forEach(function (row, i) {
    var fill  = row.querySelector('.tat-bucket-fill');
    var valEl = row.querySelector('.tat-bucket-val');
    var entry = bucket[keys[i]];

    var count  = (entry && entry[0] !== undefined) ? entry[0] : '0';
    var pctStr = (entry && entry[1] !== undefined) ? entry[1] : '0%';
    var pctNum = Math.min(parseFloat(pctStr) || 0, 100);

    fill.setAttribute('data-count', count);
    fill.setAttribute('data-pct',   pctStr);
    fill.style.width  = pctNum + '%';
    valEl.textContent = parseInt(count).toLocaleString();
    valEl.style.color = fill.getAttribute('data-color') || fill.style.background;
});
};

    // ============================================================
    //  setProductTable
    //  JSON: avg_tat_by_product.{key}.{name,avg_tat,min,max,status,status_color}
    // ============================================================
    TatDocmentManager.prototype.setProductTable = function (products) {
    var colorMap = { red: 'var(--red)', green: 'var(--green)', yellow: 'var(--amber)' };
    var pillMap  = { red: 'tat-pill-red', green: 'tat-pill-green', yellow: 'tat-pill-amber' };
    var labelMap = { red: 'Breached', green: 'Within TAT', yellow: 'At Risk' };

    var tbody = this.tat_table.querySelector('tbody');
    tbody.innerHTML = '';

    Object.keys(products).forEach(function (key) {
    var p   = products[key];
    var col = colorMap[p.status_color]  || 'var(--muted)';
    var pill= pillMap[p.status_color]   || '';
    var lbl = labelMap[p.status_color]  || p.status;
    tbody.innerHTML +=
    '<tr>' +
    '<td style="font-weight:700">' + p.name + '</td>' +
    '<td><span style="color:' + col + ';font-family:\'JetBrains Mono\',monospace;font-weight:600">' + p.avg_tat + '</span></td>' +
    '<td style="color:var(--muted)">' + p.min + ' HRS</td>' +
    '<td style="color:var(--muted)">' + p.max + ' HRS</td>' +
    '<td><span class="tat-pill ' + pill + '">' + lbl + '</span></td>' +
    '</tr>';
});
};

    // ============================================================
    //  setZoneData
    //  JSON: zone_wise_tat = [{name, per}, ...]
    // ============================================================
    TatDocmentManager.prototype.setZoneData = function (zones) {
    var colorScale = ['#e8344a', '#e6900a', '#2355f5', '#0aaa6e', '#7c3aed'];
    var html = '';
    zones.forEach(function (z, idx) {
    var pctNum = Math.min(parseInt(z.per) || 0, 100);
    var col    = colorScale[idx % colorScale.length];
    html +=
    '<div>' +
    '<div class="tat-zone-row">' +
    '<span>' + z.name + ' Zone</span>' +
    '<span class="tat-zone-pct" style="color:' + col + '">' + z.per + '</span>' +
    '</div>' +
    '<div class="tat-prog-bg"><div class="tat-prog-fill" style="width:' + pctNum + '%;background:' + col + '"></div></div>' +
    '</div>';
});
    this.tat_zone_list.innerHTML = html;
};

    TatDocmentManager.prototype.setAllData = function (data) {
    if (data.card_data)           this.setCardData(data.card_data);
    if (data.line_chart)          drawTrendChart(data.line_chart.x_axis, data.line_chart.series);
    if (data.call_by_tat_1)       this.setBucketDataById_tat1('tatBucketWrap',   data.call_by_tat_1);
    if (data.call_by_tat_2)       this.setBucketDataById_tat2('tatBucketWrap-2', data.call_by_tat_2);
    if (data.call_by_tat_3)       this.setBucketDataById_tat3('tatBucketWrap-3', data.call_by_tat_3);
    if (data.avg_tat_by_product)  this.setProductTable(data.avg_tat_by_product);
    if (data.zone_wise_tat)       this.setZoneData(data.zone_wise_tat);
};

    // ============================================================
    //  formSubmit
    // ============================================================
    TatDocmentManager.prototype.formSubmit = function () {
    var self = this;

    if (!navigator.onLine) {
    this.showMessage('No internet connection detected', 'error');
    return;
}

    this.hideDashboard();
    this.showLoader();
    this.submit_button.disabled = true;

    var formdata = new FormData();
    formdata.set('data_range',    this.dataRange   ? this.dataRange.value   : '');
    formdata.set('zone',          this.zone        ? this.zone.value        : '');
    formdata.set('state',         this.state       ? this.state.value       : '');
    formdata.set('bsi',           this.bsi         ? this.bsi.value         : '');
    formdata.set('enginner_type', this.enginertype ? this.enginertype.value : '');
    formdata.set('product',       this.product     ? this.product.value     : '');
    formdata.set('tat_data', '1');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../pagination/tat-call-data-grid.php', true);
    xhr.timeout = 150000;

    xhr.ontimeout = function () {
    self.submit_button.disabled = false;
    self.hideLoader();
    self.showMessage('Request timed out. Server is taking too long.', 'error');
};

    xhr.onerror = function () {
    self.submit_button.disabled = false;
    self.hideLoader();
    self.showMessage('Unable to connect to server. Check your network.', 'error');
};

    xhr.onload = function () {
    self.submit_button.disabled = false;
    self.hideLoader();

    if (xhr.status < 200 || xhr.status >= 300) {
    self.showMessage('Server error (' + xhr.status + '): ' + xhr.statusText, 'error');
    return;
}

    var data;
    try {
    data = JSON.parse(xhr.responseText);
} catch (e) {
    self.showMessage('Server returned invalid data. Please try again.', 'error');
    return;
}

    if (!data || (typeof data === 'object' && Object.keys(data).length === 0)) {
    self.showMessage('No data found for the selected filters.', 'warning');
    return;
}

    if (data.success === false) {
    self.showMessage(data.message || 'Something went wrong on the server.', 'error');
    return;
}

    self.setAllData(data);
    self.showDashboard();
};

    xhr.send(formdata);
};

    // ============================================================
    //  Boot
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
    var tat = new TatDocmentManager();
    tat.init();
    tat.bindEvent();
    tat.hideDashboard();
});
    function Subscriber(){}
    function Observer(){
    }
    Observer.prototype.attach=function(){
        throw new Error('Method not implemented.');
    }
    Observer.prototype.disconnect=function(){
        throw new Error("Method not implemented.");
    }
    Observer.prototype.setData=function(){
        throw new Error("Method not implemented.");
    }
    Subscriber.prototype.update=function(){
        throw new Error("Method not implemented.");
    }
<?php
$pagination_check="customer_form.php?customer_form=";
$pagination_sumit="customer_form.php?submit=true";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Lectrix – Customer Service Feedback</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:    #e63946;
            --red-d:  #c1121f;
            --green:  #2dc653;
            --yellow: #f4b942;
            --bg:     #f4f6f3;
            --card:   #ffffff;
            --text:   #1a1a2e;
            --muted:  #6b7280;
            --border: #d1d5db;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 24px 12px 40px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 780px;
            background: var(--card);
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10), 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .stripe-top {
            height: 5px;
            background: linear-gradient(90deg, var(--red-d), var(--red), #ff6b6b);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 28px 16px;
            background: #fff;
            gap: 16px;
        }

        .header-logo img {
            height: 56px;
            width: auto;
            object-fit: contain;
        }
        .header-logo .fallback {
            height: 56px;
            width: 90px;
            background: var(--red);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 1px;
            display: none;
        }

        .header-center {
            flex: 1;
            text-align: center;
        }
        .header-center h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--red);
            text-decoration: underline;
            text-underline-offset: 4px;
            letter-spacing: 0.3px;
        }
        .header-center p {
            font-size: 0.78rem;
            color: #2d6a4f;
            font-weight: 600;
            margin-top: 5px;
            line-height: 1.4;
        }

        .header-scooter img {
            height: 80px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(2px 4px 8px rgba(0,0,0,0.15));
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 0 28px;
        }

        .form-body {
            padding: 24px 28px 28px;
        }

        .input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 20px;
            margin-bottom: 20px;
        }


        .field label {
            display: block;
            font-size: 0.74rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }
        .field label .req { color: var(--red); margin-left: 2px; }

        .field input {
            width: 100%;
            border: 1.8px solid var(--border);
            border-radius: 9px;
            padding: 9px 12px;
            font-size: 0.83rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: #fafafa;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .field input:focus {
            outline: none;
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(230,57,70,0.10);
            background: #fff;
        }

        .qs-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0 24px;
            align-items: start;
            margin-bottom: 20px;
        }

        .questions-col { display: flex; flex-direction: column; gap: 18px; }

        .scooter-col {
            width: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 4px;
        }
        .scooter-col img {
            width: 100%;
            height: auto;
            object-fit: contain;
            animation: float 3.5s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }

        .q-block p {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .q-block p .req { color: var(--red); }

        .rating-row {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }

        .rating-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
        }
        .rating-labels span {
            font-size: 0.66rem;
            color: var(--muted);
            font-weight: 500;
        }

        .r-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid transparent;
            font-size: 0.78rem;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            transition: all 0.16s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            position: relative;
        }

        .r-btn[data-val="0"],
        .r-btn[data-val="1"],
        .r-btn[data-val="2"],
        .r-btn[data-val="3"],
        .r-btn[data-val="4"],
        .r-btn[data-val="5"],
        .r-btn[data-val="6"] { background: #e63946; }

        .r-btn[data-val="7"],
        .r-btn[data-val="8"]  { background: #f4b942; color: #1a1a2e; }

        .r-btn[data-val="9"],
        .r-btn[data-val="10"] { background: #2dc653; }

        .r-btn:hover  { transform: scale(1.15); box-shadow: 0 3px 10px rgba(0,0,0,0.20); }
        .r-btn.active { transform: scale(1.22); box-shadow: 0 4px 14px rgba(0,0,0,0.30); border-color: #fff; outline: 2.5px solid currentColor; }

        .rating-wrap { position: relative; }
        .rating-wrap .icon-bad,
        .rating-wrap .icon-good {
            font-size: 1.1rem;
            opacity: 0.55;
            user-select: none;
        }

        .sec-divider {
            border: none;

            margin: 20px 0;
        }

        .textarea-wrap { position: relative; }
        .textarea-wrap textarea {
            width: 100%;
            border: 1.8px solid var(--border);
            border-radius: 9px;
            padding: 10px 12px;
            font-size: 0.82rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: #fafafa;
            resize: vertical;
            min-height: 90px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .textarea-wrap textarea:focus {
            outline: none;
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(230,57,70,0.10);
            background: #fff;
        }
        .char-count {
            position: absolute;
            bottom: 8px;
            right: 10px;
            font-size: 0.68rem;
            color: var(--muted);
            pointer-events: none;
            transition: color 0.2s;
        }

        .error-msg {
            display: none;
            font-size: 0.78rem;
            color: var(--red);
            font-weight: 600;
            background: #fff5f5;
            border: 1.5px solid #fca5a5;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 14px;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: darkblue;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(230,57,70,0.35);
        }
        .submit-btn:hover {
            background: blue;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230,57,70,0.45);
        }
        .submit-btn:active { transform: translateY(0); }
        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .stripe-bot {
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, var(--red), var(--red-d));
        }

        /* ── Card Footer Logo ── */
        .card-footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px 18px;
            background: #fff;
            border-top: 1.5px dashed var(--border);
            gap: 10px;
        }
        .card-footer-logo img {
            height: 36px;
            width: auto;
            object-fit: contain;
            opacity: 0.85;
        }
        .card-footer-logo span {
            font-size: 0.7rem;
            color: var(--muted);
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .footer {
            text-align: center;
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 14px;
        }

        #successScreen {
            display: none;
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 780px;
        }

        .success-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.10);
            overflow: hidden;
            text-align: center;
            padding: 60px 40px 50px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2dc653, #1a9e3f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 8px 24px rgba(45,198,83,0.35);
            animation: pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        }
        @keyframes pop {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .success-icon svg {
            width: 40px; height: 40px; stroke: #fff; fill: none;
            stroke-width: 3; stroke-linecap: round; stroke-linejoin: round;
        }

        .success-card h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 10px;
        }
        .success-card .sub {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.6;
            max-width: 360px;
            margin: 0 auto 30px;
        }

        .stars {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 28px;
        }
        .stars span {
            font-size: 1.8rem;
            animation: starPop 0.4s ease both;
        }
        .stars span:nth-child(1) { animation-delay: 0.1s; }
        .stars span:nth-child(2) { animation-delay: 0.2s; }
        .stars span:nth-child(3) { animation-delay: 0.3s; }
        .stars span:nth-child(4) { animation-delay: 0.4s; }
        .stars span:nth-child(5) { animation-delay: 0.5s; }
        @keyframes starPop {
            from { transform: scale(0) rotate(-20deg); opacity: 0; }
            to   { transform: scale(1) rotate(0); opacity: 1; }
        }

        .new-btn {
            padding: 12px 36px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--red), var(--red-d));
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(230,57,70,0.30);
        }
        .new-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230,57,70,0.40);
        }

        @media (max-width: 540px) {
            body{
                padding: 10px;
            }
            .card{
                border-radius: 14px;
            }
            .form-body{
                padding: 18px 14px 22px;
            }

            .input-grid{
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            /* Dealer location full width */
            .field[style*="grid-column"]{
                grid-column: 1 / -1 !important;
            }

            .field label{
                font-size: 0.68rem;
            }

            .field input{
                font-size: 0.74rem;
                padding: 8px 10px;
            }

            /* Header */
            .header{
                padding: 12px !important;
            }

            .header-center h1{
                font-size: 1rem;
                line-height: 1.3;
            }

            .header-center p{
                font-size: 0.70rem;
            }

            /* Questions section */
            .qs-grid{
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .questions-col{
                gap: 20px;
            }

            .scooter-col{
                display: none;
            }

            .q-block p{
                font-size: 0.76rem;
                line-height: 1.5;
            }
            .rating-row{
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 8px;
                width: 100%;
            }

            .r-btn{
                width: 100%;
                aspect-ratio: 1/1;
                height: auto;
                font-size: 0.75rem;
            }

            .textarea-wrap textarea{
                font-size: 0.76rem;
            }

            .submit-btn{
                font-size: 0.82rem;
                padding: 12px;
            }

            /* Watermark */
            .lectrix-bg{
                background-size: 65% !important;
                background-position: center 48% !important;
            }
        }
    </style>
</head>
<body>


<div class="card" id="formCard" style="position:relative;top:0px;left:0px;">
    <div>
        <div class="stripe-top"></div>

        <div class="header" style="padding:0px!important;margin:5px;">
            <div class="header-logo">
                <img src="../img/ev_logo.png" style="height:5rem;width:100%;" alt="Lectrix"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                <div class="fallback">LECTRIX</div>
            </div>
        </div>

        <!-- Header -->
        <!-- Header -->
        <div class="header">
            <!-- Center Content -->
            <div class="header-center">
                <h1>Customer Service Feedback Form</h1>
                <p>
                    Thank you for choosing our service.
                    We would appreciate your feedback to help us improve.
                </p>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Form -->
        <form id="feedbackForm" class="form-body">

            <!-- Input Grid -->
            <div class="input-grid">
                <div class="field">
                    <label>Customer Name <span class="req">*</span></label>
                    <input type="text" id="customerName" placeholder="Enter customer name" value="Manu pathak" disabled
                           style="border:none;background:transparent;"/>
                </div>
                <div class="field">
                    <label>Job Card Number <span class="req">*</span></label>
                    <input type="text" id="jobCardNumber" placeholder="Enter job card number" value="B00234234K" disabled
                           style="border:none;background:transparent;"/>
                </div>
                <div class="field">
                    <label>Job Card Close Date <span class="req">*</span></label>
                    <input type="date" id="jobCardDate" value="<?=date('Y-m-d')?>" disabled
                           style="border:none;background:transparent;"/>
                </div>
                <div class="field">
                    <label>Dealer Name <span class="req">*</span></label>
                    <input type="text" id="dealerName" placeholder="Enter dealer name" value="UPPAL Noida" disabled
                           style="border:none;background:transparent;"/>
                </div>
                <div class="field" style="grid-column:1/-1;">
                    <label>Dealer Location <span class="req">*</span></label>
                    <input type="text" id="dealerLocation" placeholder="Enter dealer location" value="Noida Sector 8" disabled
                           style="border:none;background:transparent;"/>
                </div>
            </div>

            <div class="sec-divider"></div>

            <!-- Questions + Scooter Grid -->
            <div class="qs-grid" >
                <div class="questions-col">

                    <!-- Q1 -->
                    <div style="margin-top: 20px;" class="q-block">
                        <p>Q. How would you rate your overall service experience? <span class="req">*</span></p>
                        <div class="rating-row" id="r1"></div>
                        <input type="hidden" id="r1val"/>
                    </div>

                    <!-- Q2 -->
                    <div class="q-block">
                        <p>Q. How satisfied were you with the staff's behaviour? <span class="req">*</span></p>
                        <div class="rating-row" id="r2"></div>
                        <input type="hidden" id="r2val"/>
                    </div>

                    <!-- Q3 -->
                    <div class="q-block">
                        <p>Q. Was the service completed on time? <span class="req">*</span></p>
                        <div class="rating-row" id="r3"></div>
                        <input type="hidden" id="r3val"/>
                    </div>

                </div>

                <!-- Scooter Image -->
                <div class="scooter-col">
                    <img src="../img/scrooter_image.png" alt="EV Scooter"
                         onerror="this.src='../img/ev_logo.png'"/>
                </div>
            </div>

            <div class="sec-divider"></div>

            <!-- Comments -->
            <div class="q-block" style="margin-bottom:18px;">
                <p>Q. Please share any comments or suggestions:
                    <span style="color:var(--muted);font-weight:400;">(Optional)</span></p>
                <div class="textarea-wrap">
                    <textarea id="comments" maxlength="15000"
                              placeholder="Write your feedback here… (max 15000 characters)"
                              rows="4"
                              oninput="updateChar(this)"></textarea>
                    <span class="char-count" id="charCount">0 / 15000</span>
                </div>
            </div>

            <!-- Error -->
            <div class="error-msg" id="errMsg">⚠️ Please fill all required fields and select a rating for each question.</div>

            <!-- Submit -->
            <button type="submit" class="submit-btn" id="submitBtn">Submit Feedback</button>
        </form>

        <!-- ── Card Footer Logo ── -->
        <div class="card-footer-logo">
            <img src="../img/ev_logo.png" alt="Lectrix"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline'"/>
        </div>

        <div class="stripe-bot"></div>
    </div>

    <!-- Watermark background -->
    <div class="lectrix-bg"
         style="position:absolute;top:0px;left:0px;background:transparent;
        background-image:url('../img/Lectrix Logo.png');
        background-position:center 40%;
        background-size:55%;
        background-repeat:no-repeat;
        filter:brightness(80%);width:100%;height:100%;z-index:-1"></div>
    <style>
        @media (max-width:768px){
            .lectrix-bg{
                background-size:55% !important;
                background-position:center 34% !important;
            }
        }
        @media (max-width:480px){
            .lectrix-bg{
                background-size:50% !important;
                background-position:center 50% !important;
            }
        }
    </style>
</div>

<!-- ════════════ SUCCESS SCREEN ════════════ -->
<div id="successScreen">
    <div class="success-card">
        <div class="success-icon">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2>Thank You! 🎉</h2>
        <p class="sub">Your feedback has been submitted successfully. We appreciate you taking the time to share your experience with us.</p>
        <div class="stars">
            <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
        </div>
    </div>
    <p class="footer" style="margin-top:14px;">© <span id="yr2"></span> Lectrix EV — All Rights Reserved</p>
</div>

<p style="display:none;"  id="footerMain"></p>

<script>
    //  Year
    const y = new Date().getFullYear();
    // document.getElementById('yr').textContent  = y;
    document.getElementById('yr2').textContent = y;

    //  Build rating buttons 0–10
    ['r1','r2','r3'].forEach(gid => {
        const wrap = document.getElementById(gid);
        for (let i = 0; i <= 10; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'r-btn';
            btn.dataset.val = i;
            btn.textContent = i;
            btn.addEventListener('click', () => pick(gid, i));
            wrap.appendChild(btn);
        }
    });

    function pick(gid, val) {
        document.getElementById(gid).querySelectorAll('.r-btn').forEach(b => {
            b.classList.toggle('active', +b.dataset.val === val);
        });
        document.getElementById(gid + 'val').value = val;
    }

    function updateChar(el) {
        const n   = el.value.length;
        const el2 = document.getElementById('charCount');
        el2.textContent = n + ' / 15000';
        el2.style.color = n > 14000 ? '#e63946' : n > 12000 ? '#f4b942' : '#9ca3af';
    }

    //  Show / Hide helpers
    function showForm() {
        document.getElementById('formCard').style.display     = 'block';
        document.getElementById('successScreen').style.display = 'none';
    }
    function showSuccess() {
        document.getElementById('formCard').style.display      = 'none';
        document.getElementById('successScreen').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    //  FormManager
    function FormManager() {}

    FormManager.prototype.checkAlreadyFormSubmitOrNot = async function () {
        // customer_id URL param se lena — fallback C001
        const urlParams  = new URLSearchParams(window.location.search);
        const customerId = urlParams.get('customer_id') || 'C001';

        try {
            const url      = `<?=$pagination_check?>${customerId}`;
            const response = await fetch(url);
            const data     = await response.json();

            if (data === null || data === undefined) {
                throw new Error("No data from server");
            }

            // status: true  → already submitted → success
            // status: false → not yet submitted  → form
            if (data.status === true) {
                showSuccess();
            } else {
                showForm();
            }
        } catch (err) {
            console.warn('Check failed, showing form:', err);
            showForm();
        }
    };

    // Form Submit
    document.getElementById('feedbackForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validation — ratings required
        const r1 = document.getElementById('r1val').value;
        const r2 = document.getElementById('r2val').value;
        const r3 = document.getElementById('r3val').value;
        const errEl = document.getElementById('errMsg');

        if (r1 === '' || r2 === '' || r3 === '') {
            errEl.style.display = 'block';
            errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        errEl.style.display = 'none';

        // Collect field values

        const urlParams  = new URLSearchParams(window.location.search);
        const _customerId_ = urlParams.get('customer_id') || 'C001';

        const jobNo      = document.getElementById('jobCardNumber').value.trim();   // B00234234K
        const customerId = _customerId_;  // hardcoded
        const custName   = document.getElementById('customerName').value.trim();
        const closeDate  = document.getElementById('jobCardDate').value;             // YYYY-MM-DD
        const dealerCode = 'D001';  // hardcoded
        const comments   = document.getElementById('comments').value.trim();

        // Format: job_no<jobNo>=&customer_id=...&...
        const params = new URLSearchParams();
        params.append(`job_no`, jobNo);
        params.append('customer_id',          customerId);
        params.append('customer_name',        custName);
        params.append('job_card_closed_date', closeDate);
        params.append('dealer_code',          dealerCode);
        params.append('r1',                   r1);
        params.append('r2',                   r2);
        params.append('r3',                   r3);
        params.append('comments',             comments);

        // Disable button to prevent double submit
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled    = true;
        submitBtn.textContent = 'Submitting…';

        try {
            const response = await fetch(`<?=$pagination_sumit?>`, {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    params.toString()
            });

            // Response check (adjust as per your backend)
            if (response.ok) {
                showSuccess();
            } else {
                throw new Error('Server error: ' + response.status);
            }
        } catch (err) {
            console.error('Submit failed:', err);
            errEl.textContent    = '⚠️ Submission failed. Please try again.';
            errEl.style.display  = 'block';
            submitBtn.disabled   = false;
            submitBtn.textContent = 'Submit Feedback';
        }
    });

    //  On page load  check if already submitted
    document.addEventListener('DOMContentLoaded', function () {
        // Initially hide both until check completes
        document.getElementById('formCard').style.display      = 'none';
        document.getElementById('successScreen').style.display = 'none';

        const fm = new FormManager();
        fm.checkAlreadyFormSubmitOrNot();
    });
</script>
</body>
</html>
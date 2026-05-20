
    const billingform=document.getElementById("billingform");
    const customerName=document.getElementById("customerName");
    const state=document.getElementById("state");
    const gstin=document.getElementById("customergstn");
    const contactno=document.getElementById("customercontactno");
    const email=document.getElementById("customeremail");

    // modal initialization


    const product_master = document.getElementById("product_master");
    const brand_master = document.getElementById("brand_master");
    const model_master = document.getElementById("model_master");
    const part_master = document.getElementById("part_master");
    const serial_no = document.getElementById("serial_no");

    /* ===== Quantity & Pricing ===== */
    const qty_modal = document.getElementById("qty_modal");
    const price_modal = document.getElementById("price_modal");
    const cost_modal = document.getElementById("cost_modal");
    const discount_modal = document.getElementById("discount_modal");

    /* ===== Tax & Total ===== */
    const value_after_discount = document.getElementById("value_after_discount");
    const igst_per = document.getElementById("igst_per");
    const igst_amt  = document.getElementById("igst_amt");
    const total = document.getElementById("total");



    function openModal() {
    document.getElementById('invoiceModal').classList.add('active');
}

    function closeModal() {
    document.getElementById('invoiceModal').classList.remove('active');
}
    function validateForm() {

    if (!billingform.value || billingform.value === '#') {
    showError("Please select Billing From");
    return false;
}

    if (!customerName.value.trim()) {
    showError("Customer Name cannot be empty");
    return false;
}

    if (!state.value) {
    showError("Please select State");
    return false;
}

    // 📞 Contact Number validation
    if (!contactno.value.trim()) {
    showError("Contact Number is required");
    return false;
}

    if (!/^[0-9]{10}$/.test(contactno.value)) {
    showError("Contact Number must be exactly 10 digits");
    return false;
}

    // 📧 Email validation
    if (!email.value.trim()) {
    showError("Email is required");
    return false;
}

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailRegex.test(email.value)) {
    showError("Please enter a valid Email address");
    return false;
}

    // 🧾 Optional GSTIN validation
    // if (gstin.value && !/^[0-9A-Z]{5}$/.test(gstin.value)) {
    //     showError("GSTIN must be 15 characters (A–Z, 0–9)");
    //     return false;
    // }

    return true;
}

    function showError(msg) {
    document.getElementById("errorMessage").innerText = msg;
    document.getElementById("errorModal").classList.add("active");
}

    function closeErrorModal() {
    document.getElementById("errorModal").classList.remove("active");
}

    // Button click
    function handleAddInvoice() {
    // if (validateForm()) {
    //     openModal();
    // }
    openModal();
}

    function product(){
        let product = product_master.value;
        brand_master.value = "0";
        model_master.innerHTML = '<option value="">Select Model</option>';
        part_master.innerHTML  = '<option value="">Select Part</option>';
        serial_no.value = "000-000-000";
        qty_modal.value = "0";
        price_modal.value = "0";
        cost_modal.value = "0";
        discount_modal.value = "0";
        value_after_discount.value = "0.0";
        igst_per.value = "0";
        igst_amt.value = "0.0";
        total.value = "0.0";
        if(product !== ""){
            document.getElementById("brand_master").disabled = false;
        } else {
            document.getElementById("brand_master").disabled = true;
        }
    }
    async function brand(brand_data,product_data){
        let brand=brand_master.value;
        if(brand!==""){
            model_master.disabled=false;
           await setModalData(brand,product_master.value);
        }else{
            model_master.disabled=true;
        }
    }
    async function setModalData(brand, product) {
        try {
            const response = await sendRequest("", {
                brand: brand,
                productid: product
            });

            if (!response.status) return;

            model_master.innerHTML = '<option value="">Select Model</option>';
            model_master.innerHTML += response.options;
            model_master.disabled = false;

        } catch (err) {
            console.error("Model load error:", err);
        }
    }

    async function model(asc_cde){
        const modal=model_master.value;
        if(modal!==""){
            part_master.disabled=false;
           await setPartCodeData(modal,asc_cde);
        }
        else{
            part_master.disabled=true;
        }
    }
    async function setPartCodeData(model_id,asc_code){
        try {
            const response = await sendRequest("", {
                modelid: model_id,
                asc_code: asc_code
            });

            if (!response.status) return;

            part_master.innerHTML = '<option value="">Select Model</option>';
            part_master.innerHTML += response.options;
            part_master.disabled = false;

        } catch (err) {
            console.error("Model load error:", err);
        }
    }
    function partcode(){
        const partcode=part_master.value;
        console.log(partcode);
        if(partcode!==""){
            serial_no.placeholder='000-000-000';
            serial_no.value="";
            serial_no.focus(); // 👈 important
            serial_no.disabled=false;
        }else{
            serial_no.disabled=true;
        }
    }

     function serialNoCheck(value){
       console.log(value);
    }




    async function sendRequest(url, data = {}) {
        const formData = new URLSearchParams(data).toString();

        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error("Network error");
        }

        return await response.json();
    }

    // select partcode, part_name, part_category,vendor_partcode,brand_id from
    // partcode_master where
    // model_id Like '%".$_POST['modelbilling']."%' and partcode in (
    //     select partcode from client_inventory where location_code='".$_SESSION['asc_code']."'
    // and scrap > 0) ".$dup_part." group by partcode order by part_name ";

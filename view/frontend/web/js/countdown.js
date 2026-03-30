(function () {
    'use strict';

    var endTime      = parseInt(window.mvEndTime || 0, 10) * 1000;
    var subscribeUrl = window.mvSubscribeUrl || '';
    var btnLabel     = window.mvBtnLabel || 'Notify Me';

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function updateCountdown() {
        var now  = Date.now();
        var diff = endTime > now ? (endTime - now) : 0;

        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);

        var dEl = document.getElementById('mv-days');
        var hEl = document.getElementById('mv-hours');
        var mEl = document.getElementById('mv-minutes');
        var sEl = document.getElementById('mv-seconds');

        if (dEl) dEl.textContent = pad(d);
        if (hEl) hEl.textContent = pad(h);
        if (mEl) mEl.textContent = pad(m);
        if (sEl) sEl.textContent = pad(s);

        if (diff <= 0) return false;
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {

        updateCountdown();
        var timer = setInterval(function() {
            if (!updateCountdown()) clearInterval(timer);
        }, 1000);

        var btn   = document.getElementById('mv-subscribe-btn');
        var input = document.getElementById('mv-email');
        var msg   = document.getElementById('mv-subscribe-msg');

        if (!btn || !input || !subscribeUrl) return;

        btn.addEventListener('click', function () {
            var email = (input.value || '').trim();

            var formKeyEl = document.querySelector('input[name="form_key"]');
            var formKey   = formKeyEl ? formKeyEl.value : '';

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showMsg(msg, 'Please enter a valid email address.', false);
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Sending...';

            fetch(subscribeUrl, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'email=' + encodeURIComponent(email) + '&form_key=' + encodeURIComponent(formKey)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                showMsg(msg, data.message, data.success);
                if (data.success) input.value = '';
            })
            .catch(function() {
                showMsg(msg, 'Network error. Please try again.', false);
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = btnLabel;
            });
        });
    });

    function showMsg(msgEl, text, success) {
        if (!msgEl) return;
        msgEl.textContent   = text;
        msgEl.style.display = 'block';
        msgEl.className     = 'cs-sub-msg ' + (success ? 'success' : 'error');

        setTimeout(function () { 
            msgEl.style.display = 'none'; 
        }, 5000);
    }

})();
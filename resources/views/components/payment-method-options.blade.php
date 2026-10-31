<fieldset>
    <legend class="mb-2 block text-xs font-bold uppercase text-gray-700" x-text="t('paymentMethod')">Metode pembayaran</legend>
    <div class="space-y-2">
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="outlet_cash" required @checked(old('payment_method', 'outlet_cash') === 'outlet_cash') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentCash')">Bayar di outlet · Cash</strong><span class="text-xs text-gray-500" x-text="t('paymentCashDescription')">Pembayaran dilakukan saat kunjungan.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="outlet_qris" required @checked(old('payment_method') === 'outlet_qris') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentQris')">Bayar di outlet · QRIS</strong><span class="text-xs text-gray-500" x-text="t('paymentQrisDescription')">Scan QRIS yang tersedia di outlet.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="bank_bca" required @checked(old('payment_method') === 'bank_bca') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentBankBca')">Transfer bank · BCA</strong><span class="text-xs text-gray-500" x-text="t('paymentBankDescription')">Detail rekening dikonfirmasi oleh tim outlet.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="bank_mandiri" required @checked(old('payment_method') === 'bank_mandiri') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentBankMandiri')">Transfer bank · Mandiri</strong><span class="text-xs text-gray-500" x-text="t('paymentBankDescription')">Detail rekening dikonfirmasi oleh tim outlet.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="ewallet_gopay" required @checked(old('payment_method') === 'ewallet_gopay') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentGopay')">E-Wallet · GoPay</strong><span class="text-xs text-gray-500" x-text="t('paymentWalletDescription')">Instruksi pembayaran diberikan setelah booking dikonfirmasi.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="ewallet_ovo" required @checked(old('payment_method') === 'ewallet_ovo') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentOvo')">E-Wallet · OVO</strong><span class="text-xs text-gray-500" x-text="t('paymentWalletDescription')">Instruksi pembayaran diberikan setelah booking dikonfirmasi.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="ewallet_dana" required @checked(old('payment_method') === 'ewallet_dana') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900" x-text="t('paymentDana')">E-Wallet · DANA</strong><span class="text-xs text-gray-500" x-text="t('paymentWalletDescription')">Instruksi pembayaran diberikan setelah booking dikonfirmasi.</span></span>
        </label>
    </div>
    <p class="mt-2 text-xs leading-relaxed text-gray-500" x-text="t('paymentPendingNotice')">Pilihan tersimpan sebagai metode pembayaran. Pembayaran belum diproses otomatis dan masih menunggu konfirmasi.</p>
</fieldset>

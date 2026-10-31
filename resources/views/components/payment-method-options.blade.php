<fieldset>
    <legend class="mb-2 block text-xs font-bold uppercase text-gray-700">Metode pembayaran</legend>
    <div class="space-y-2">
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="outlet_cash" required @checked(old('payment_method', 'outlet_cash') === 'outlet_cash') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">Bayar di outlet · Cash</strong><span class="text-xs text-gray-500">Pembayaran dilakukan saat kunjungan.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="outlet_qris" required @checked(old('payment_method') === 'outlet_qris') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">Bayar di outlet · QRIS</strong><span class="text-xs text-gray-500">Scan QRIS yang tersedia di outlet.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="bank_bca" required @checked(old('payment_method') === 'bank_bca') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">Transfer bank · BCA</strong><span class="text-xs text-gray-500">Detail rekening dikonfirmasi oleh tim outlet.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="bank_mandiri" required @checked(old('payment_method') === 'bank_mandiri') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">Transfer bank · Mandiri</strong><span class="text-xs text-gray-500">Detail rekening dikonfirmasi oleh tim outlet.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="ewallet_gopay" required @checked(old('payment_method') === 'ewallet_gopay') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">E-Wallet · GoPay</strong><span class="text-xs text-gray-500">Instruksi pembayaran diberikan setelah booking dikonfirmasi.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="ewallet_ovo" required @checked(old('payment_method') === 'ewallet_ovo') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">E-Wallet · OVO</strong><span class="text-xs text-gray-500">Instruksi pembayaran diberikan setelah booking dikonfirmasi.</span></span>
        </label>
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50/50">
            <input type="radio" name="payment_method" value="ewallet_dana" required @checked(old('payment_method') === 'ewallet_dana') class="mt-1 accent-blue-900">
            <span class="text-sm"><strong class="block text-gray-900">E-Wallet · DANA</strong><span class="text-xs text-gray-500">Instruksi pembayaran diberikan setelah booking dikonfirmasi.</span></span>
        </label>
    </div>
    <p class="mt-2 text-xs leading-relaxed text-gray-500">Pilihan tersimpan sebagai metode pembayaran. Pembayaran belum diproses otomatis dan masih menunggu konfirmasi.</p>
</fieldset>

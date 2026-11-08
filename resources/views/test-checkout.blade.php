<!-- Add this to your test blade file -->
<form action="{{ url('payway/checkout') }}" method="POST">
    @csrf
    <!-- Replace '1' with an actual booking ID that exists in your database -->
    <input type="hidden" name="booking_id" value="1">
    <!-- Add this above your test checkout button in your blade view -->
    @error('payway_error')
        <div style="color: red; margin-bottom: 15px;">
            Error: {{ $message }}
        </div>
    @enderror
    <button type="submit" style="padding: 10px 20px; background: #00bcd4; color: white; border: none; cursor: pointer;">
        Test PayWay Checkout
    </button>
</form>
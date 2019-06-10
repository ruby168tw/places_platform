<form action={{ route('check_sending_times') }} method="post">
    @csrf
    手機:<input type="text" name="phone">
    <button type="submit">登入</button>
    </form>
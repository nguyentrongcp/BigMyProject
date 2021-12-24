<!DOCTYPE html>
<html>
<script src="/giaodien/plugins/jquery/jquery.min.js"></script>
<script src="/giaodien/my_plugins/cookie/cookie.min.js"></script>
<script>
    let token = Cookies.get('token');
    if (token == null || token === '') {
        token = localStorage.getItem('token');
        if (token == null || token === '') {
            location.href = '/nong-dan/dang-nhap';
        }
        else {
            Cookies.set('token',token);
        }
    }
    location.href = '/nong-dan/thua-ruong';
</script>
</html>

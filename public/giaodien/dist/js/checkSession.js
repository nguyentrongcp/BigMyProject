let token = Cookies.get('token');
if (token == null || token === '') {
    token = localStorage.getItem('token');
    if (token == null || token === '') {
        location.href = '/mobile/dang-nhap';
    }
    else {
        Cookies.set('token',token);
    }
}
location.href = '/mobile/lichsu-diemdanh';

let token = Cookies.get('token');
if (token == null || token === '') {
    token = localStorage.getItem('token');
    if (!(token == null || token === '')) {
        Cookies.set('token',token);
    }
}
if (token == null || token === '') {
    location.href = '/mobile/dang-nhap';
}
else {
    let loginType = localStorage.getItem('loginType');
    if (loginType !== 'nong-dan') {
        location.href = '/mobile/lichsu-diemdanh';
    }
    else {
        location.href = '/nong-dan/quytrinh-hientai';
    }
}

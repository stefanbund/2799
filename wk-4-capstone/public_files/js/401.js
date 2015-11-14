window.seconds = 10; 
window.onload = function()
{
    if (window.seconds != 0)
    {
        document.getElementById('secondsDisplay').innerHTML = '' +
            window.seconds + ' second' + ((window.seconds > 1) ? 's' : ''); 
        window.seconds--;
        setTimeout(window.onload, 1000);
    }
    else
    {
        window.location = 'login.php';
    }
}

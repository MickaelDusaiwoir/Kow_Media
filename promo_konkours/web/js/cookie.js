// JavaScript Document

/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setcookie (name, value, expires, path, domain, secure)
{
	var cookieContent = name + "=" + escape(value);
	
	if (expires)
	{
		var date = new Date();
		date.setTime(expires * 1000);
		
		cookieContent += '; expires=' + date.toGMTString();
	}
	
	if (path)
		cookieContent += '; path=' + path;

	if (domain)
		cookieContent += '; domain=' + domain;

	if (secure)
		cookieContent += '; secure';

    document.cookie = cookieContent;
}

/**
 * Gets the value of the specified cookie.
 *
 * name  Name of the desired cookie.
 *
 * Returns a string containing value of specified cookie,
 *   or null if cookie does not exist.
 */
function cookie (name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	
	for(var i = 0; i < ca.length; i++)
	{
		var c = ca[i];
		
		while (c.charAt(0)==' ')
			c = c.substring(1,c.length);
		
		if (c.indexOf(nameEQ) == 0)
			return c.substring(nameEQ.length,c.length);
	}
	
	return null;
}

/**
 * Deletes the specified cookie.
 *
 * name      name of the cookie
 * [path]    path of the cookie (must be same as path used to create cookie)
 * [domain]  domain of the cookie (must be same as domain used to create cookie)
 */
function delete_cookie (name, path, domain)
{
    if (getCookie(name))
	{
        document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}

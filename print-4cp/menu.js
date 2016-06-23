<? 
	require_once("/home/databiz/public_html/print-forms/inc/config.inc");
	header("Content-Type: application/x-javascript");
?>
// Open/Close single elements
function P7_TMenu(b, og)
{ //v2.5 by Project Seven Development(PVII)
	var i, s, c, k, j, tN, hh;
	if(document.getElementById)
	{
		if(b.parentNode && b.parentNode.childNodes)
		{
			tN = b.parentNode.childNodes;
		}
		else
		{
			return;
		}
		for(i = 0; i < tN.length; i++)
		{
			if(tN[i].tagName == "DIV")
			{
				s = tN[i].style.display;
				hh = (s == "block") ? "none" : "block";
				if(og == 1)
				{
					hh = "block";
				}
				tN[i].style.display = hh;
			}
		}
		c = b.firstChild;
		if(c.data)
		{
			k = c.data;
			j = k.charAt(0);
			if(j == '+')
			{
				k = '-' + k.substring(1, k.length);
			}
			else if(j == '-')
			{
				k = '+' + k.substring(1, k.length);
			}
			c.data = k;
		}
		if(b.className == 'p7plusmark')
		{
			b.className = 'p7minusmark';
		}
		else if(b.className == 'p7minusmark')
		{
			b.className = 'p7plusmark';
		}
	}
}

// Close all menu items on load. p7TMnav (div div, div div div, div div div div, ...)
function P7_setTMenu()
{ //v2.5 by Project Seven Development(PVII)
	var i, d = '', h = '<style type=\"text/css\">';
	if(document.getElementById)
	{
		var tA = navigator.userAgent.toLowerCase();
		if(window.opera)
		{
			if(tA.indexOf("opera 5") > -1 || tA.indexOf("opera 6") > -1)
			{
				return;
			}
		}
		for(i = 1; i < 20; i++)
		{
			d += 'div ';
			h += "\n#p7TMnav div " + d + "{display:none;}";
		}
		document.write(h + "\n</style>");
	}
}
P7_setTMenu();

// Open current URL menu-item on load
function P7_TMopen()
{ //v2.5 by Project Seven Development(PVII)
	var i, x, d, hr, ha, ef, a, b, c, ag, hrg;
	if(document.getElementById)
	{
		d = document.getElementById('p7TMnav');
		if(d)
		{
			hr = window.location.href;
			ha = d.getElementsByTagName("A");
			if(ha && ha.length)
			{
				for(i = 0; i < ha.length; i++)
				{
					if(ha[i].href)
					{
						if(hr.indexOf(ha[i].href) > -1)
						{
							ha[i].className = "p7currentmark";
							if(ha[i].onclick)
							{
								hrg = ha[i].onclick.toString();
								if(hrg && hrg.indexOf("P7_TMenu") > -1)
								{
									P7_TMenu(ha[i], 1);
								}
							}
							c = ha[i]; // link
							b = c.parentNode; // link parent
							a = b.parentNode; // link parent parent
							P7_TMallLower(b, 1);
							while(a && a != d)
							{
								if(a.firstChild && a.firstChild.tagName == "A")
								{
									if(a.firstChild.onclick)
									{
										ag = a.firstChild.onclick.toString();
										if(ag && ag.indexOf("P7_TMenu") > -1)
										{
											P7_TMenu(a.firstChild, 1);
										}
									}
								}
								c = b;
								b = a;
								a = a.parentNode;
							}
						}
					}
				}
			}
		}
	}
}

// Open all children of an element
function P7_TMallLower(a, o)
{
	var i, x, ha, s, tN;
	if(document.getElementById && a && a.childNodes)
	{
		ha = a.getElementsByTagName("A");
		for(i = 0; i < ha.length; i++)
		{
			if(ha[i].onclick)
			{
				ag = ha[i].onclick.toString();
				if(ag && ag.indexOf("P7_TMenu") > -1)
				{
					if(ha[i].parentNode && ha[i].parentNode.childNodes)
					{
						tN = ha[i].parentNode.childNodes;
					}
					else
					{
						break;
					}
					for(x = 0; x < tN.length; x++)
					{
						if(tN[x].tagName == "DIV")
						{
							s = tN[x].style.display;
							if(o == 1 && s != 'block')
							{
								P7_TMenu(ha[i]);
							}
							else if(o == 0 && s == 'block')
							{
								P7_TMenu(ha[i]);
							}
							break;
						}
					}
				}
			}
		}
	}
}

// Opens or closes ALL elements
function P7_TMall(a)
{ //v2.5 by Project Seven Development(PVII)
	var i, x, ha, s, tN;
	if(document.getElementById)
	{
		ha = document.getElementsByTagName("A");
		for(i = 0; i < ha.length; i++)
		{
			if(ha[i].onclick)
			{
				ag = ha[i].onclick.toString();
				if(ag && ag.indexOf("P7_TMenu") > -1)
				{
					if(ha[i].parentNode && ha[i].parentNode.childNodes)
					{
						tN = ha[i].parentNode.childNodes;
					}
					else
					{
						break;
					}
					for(x = 0; x < tN.length; x++)
					{
						if(tN[x].tagName == "DIV")
						{
							s = tN[x].style.display;
							if(a == 0 && s != 'block')
							{
								P7_TMenu(ha[i]);
							}
							else if(a == 1 && s == 'block')
							{
								P7_TMenu(ha[i]);
							}
							break;
						}
					}
				}
			}
		}
	}
}

function P7_TMclass()
{ //v2.5 by Project Seven Development(PVII)
	var i, x, d, tN, ag;
	if(document.getElementById)
	{
		d = document.getElementById('p7TMnav');
		if(d)
		{
			tN = d.getElementsByTagName("A");
			if(tN && tN.length)
			{
				for(i = 0; i < tN.length; i++)
				{
					ag = (tN[i].onclick) ? tN[i].onclick.toString() : false;
					if(ag && ag.indexOf("P7_TMenu") > -1)
					{
						tN[i].className = 'p7plusmark';
					}
					else
					{
						tN[i].className = 'p7defmark';
					}
				}
			}
		}
	}
}

function updateTime() {
	var date = new Date();
	var stunden = (date.getHours(date)-1);
	var minuten = date.getMinutes();
	var tag = date.getDate();
	var sekunden = date.getSeconds();
	var monatDesJahres = date.getMonth();
	var jahr = date.getFullYear();
	var tagInWoche = date.getDay();
	var wochentag = new Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
	var monat = new Array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");



	var datum = "Willkommen in der Fakultät Wirtschaftsingeneurwesen" + " " + "Heute" + " " + wochentag[tagInWoche] + ", " + tag + ". " + monat[monatDesJahres] + " " + jahr;
//	var datum = "Willkommen in der Fakultät Wirtschaftsingeneurwesen" + " " + "Heute" + " " + wochentag[tagInWoche] + ", " + tag + ". " + monat[monatDesJahres];
	document.getElementById('time').innerHTML = datum;
	setTimeout(updateTime, 1000);
}

window.addEventListener("load", updateTime);

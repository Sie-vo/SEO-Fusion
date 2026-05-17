# SEO-Fusion
Seo-Fusion basiert auf PHP-Fusion 7.02.xx bzw. der IUP 1.9 für PHP 8 , wo Permalinks integriert wurden.
Diese findet ihr in einer noch neuren Version in einem Repo von mir oder direkt bei https://www.phpfusion-deutschland.de/startseite.html



## Achtung noch ist dies nur eine BETA!

## Installation
wie gehabt. mod_rewrite sollte der Webserver aber können! Dann administartion/seo-save.php?mode=batch aufrufen.
Die Datenbanktabelle wird dann automatisch erstellt.
Bei einer bestehenden Webseite werden dann alle Inhalte zu SEF-URL's umgeschrieben.
Dort wird euch eine Liste angezeigt, welche Inhalte wie umgeschrieben sind. (am besten Ausdrucken oder die Seite als Quelltext speichern).
Habt ihr in der Navigation Links zu Seiten wie viewepage?id=1234 dann müsst ihr diese in der Liste suchen und entsprechend in Eurer Navigation ändern.

Die Datei .intelephense-helper.php muss nicht mit auf den Server. Diese ist nur für den Code Editor gedacht.

Eine geänderte setup.php wird noch erstellt.

## Die Zukunft
Ich habe leider zuspät gesehen, dass die IUP nicht auf der alten inoffiziellen PHP-Fusion Version von systemweb aufgebaut ist.
Heisst hier gibt es zur Zeit noch kein Bootstrap. Bootstrap und auch Fontawesome wird in Zukunft wieder integriert.
Auch werde ich noch einige andere Features aus der systemweb Version integrieren, soweit möglich.
Ich werde MySQLi in dieser Version nicht weiter unterstützen! Solange es funktioniert OK. Nur werde ich persönlich an dem Handler für MySQLi nicht weiter arbeiten.
Für PDO wird alles auf prepared Statements umgebaut. Heisst spätestens ab hier wird wohl MySQLi passe sein.

## Warum?
Ich denke PHP-Fusion ist immer noch eines der besten CMS-Systeme der Welt. Wesentlich schneller als viele der anderen Systeme. Wenn ich denn z.B. Wordpress-Seiten mir im Quelltext anschaue und sehe, dass die ersten 100 bis 1000 Codezeilen nur zum Einbinden von CSS und Javascript Dateien sind und dazu noch inline CSS und Javascript. Bis zum Footer scrollen hat man schon keinen Bock mehr, aber da sieht es dann ähnlich aus.
Zudem auch viel einfacher und vor allem intuitiver zu bedienen. Ich hatte einen Kunden, der hat viele CMS-Systeme ausprobiert. Dann habe ich ihm Fusion vor die Nase gesetzt. Da hat er gesagt: Endlich ein System mit dem von Anfang an klar kommt. Natürlich habe ich ihm ein paar kleine Tricks gezeigt, aber im Großen und ganzen kam er ohne Einweisung aus. Sprich man muss keine Wochen und Monate investieren um seinen Kunden das System beizubringen.
Warum nicht PHP-Fusion 9? 
Ganz einfach. Nick Jones der ehemalige Gründer von PHP-Fusion hat bewusst auf das KISS-Prinzip (keep it simple and stupid) gesetzt. Das ist in der 9er Version nicht mehr ganz der Fall. Und dies möchte ich hier fortführen.

Also bringen wir dieses alte System endlich auf Vordermann und ins nächste Jahrhundert!

Jeder Fusion Fan ist herzlich dazu eingeladen an diesem Projekt mit zu wirken.

## Support
Gibt es erstmal nur hier. Schreibt dazu einfach ein neus issue.
Alternativ: Natürlich schaue ich auch ins offizielle deutsche Forum.

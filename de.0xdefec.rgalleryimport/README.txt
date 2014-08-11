BITTE UNBEDINGT LESEN!!!

Einleitung
	
	Mit diesem Paket können Bilder aus einem Verzeichnis am Server in die rGallery importiert werden.
	
	Dies erleichtert den Upload von mehreren Bildern auf einmal. Wichtig ist nur zu beachten, dass für den Importer die selben Limitierungen wie für die rGallery gelten - zumindest was den Server angeht. Ein Server der das memory_limit (die Maximalmenge an Arbeitsspeicher, die ein PHP Programm konsumieren darf) auf einen zu niedrigen Wert gesetzt hat, kann auch mit dem Importer keine Bilder importieren (da dann das Verkleinern der Bilder nicht klappt).
	
	Wichtig ist auch, dass sich die maximum execution time deaktivieren lässt, besonders wenn große Mengen importiert werden. Das Script ist noch sehr rudimentär, weswegen alle Dateien auf einmal importiert werden. Wenn der Server nun jedes Script nach 30 Sekunden abbricht, können nicht alle Bilder auf einmal importiert werden.
	
	Dieses Paket unterliegt dem Urheberrecht von Andreas Diendorfer. Die Weitergabe/Verbreitung in jeglicher Form ist nicht gestattet.
	
Vorgehensweise
	1) Upload der Bilder per FTP
		Erstelle im ACP Ordner deines WBB (nicht des WCF!) einen neuen Order mit einem beliebigen Namen. Z.b. "bilderimport".
		forum/acp/bilderimport
		Lade nun alle gewünschten Bilder (Achtung! Wie auch sonst bei der rGallery nur JPG, PNG, GIF!) in den Ordner.
	2) Laden des Importers
		Der Importer befindet sich nach der Installation im ACP unter "Inhalte" -> "rGallery" -> "rGallery Import"
	3) Einstellungen
		Der unter "Importverzeichnis" einzugebende Pfad muss entweder ein absoluter Pfad sein (z.b. "/home/export/htdocs/forum/acp/bilderdepot") oder relativ auf das ACP Verzeichnis (z.b. "bilderdepot" wenn du diesen Ordner wie im Punkt 1 im ACP Ordner erstellt hast).
		Gib die userID des zukünftigen Besitzers an (siehe Benutzerliste, um die ID herauszufinden).
		Wenn du größere Bilder raufgeladen hast und diese ebenfalls zur verfügung stehen sollen, wähle die Checkbox "Orignalbilder kopieren" aus. Ansonsten werden nur die verkleinerten Versionen der rGallery aufgehoben.
		Die Auswahl der Kategorie sollte klar sein.
	4) "Absenden"
		Der Import kann einige Zeit in Anspruch nehmen - je nach Menge an Bildern. Dabei gibt es derzeit kein Feedback vom Importer, d.h. nachdem du einmal auf den "Absenden" Button geklick hast, heißt es warten. Nicht das Fenster schließen oder dich parallel im ACP anmelden! Du kannst den Fortschritt des Imports direkt in der rGallery (in einem 2ten Fenster) beobachten.
TIMESTAMP=`date +%s --date=$(date +%D)`
Start=""
End=""

if [ $# -eq 2 ]; then
	Start=$1
	End=$2
else
	echo "Abfahrt von Zuhause (DD.MM.YYYY):"
	read Start
	echo "Ankunft Zuhause (DD.MM.YYYY):"
	read End
fi

StartSplit="$(echo $Start | sed 's/\./\n/g')"
Start=$(echo "$StartSplit" | head -n2 | tail -n1)"/"$(echo "$StartSplit" | head -n1 | tail -n2)"/"$(echo "$StartSplit" | tail -n1)
StartTimestamp=$(date +%s --date="$Start")
EndSplit="$(echo $End | sed 's/\./\n/g')"
End=$(echo "$EndSplit" | head -n2 | tail -n1)"/"$(echo "$EndSplit" | head -n1 | tail -n2)"/"$(echo "$EndSplit" | tail -n1)
EndTimestamp=$(date +%s --date="$End")

DAYS=$(($(($(($EndTimestamp-$StartTimestamp))/$((3600*24))))+1))
echo $DAYS;

STUFF=()

Bad=("Bepanthen" "Melkfett" "Kondome" "Bodylotion" "Deo & Duschgel" "Gesichtsreinigungsgel" "Haare: Glätteisen" "Haare: Bürsten" "Haare: Gummis" "Haare: Schere" "Haare: Serum" "Nagelpflege" "Rasierer" "Schere" "Schminke" "Schampoo & Kur" "Wimpernserum" "Kontaktlinsen & Flüssigkeit" "Zahnbürste & -pasta & -seide" "Sonnencreme" "Brillenetui" "Menstruationstasse")
Dokumente=("Schlüssel" "Autoschlüssel & Fahrzeugschein" "Reiseunterlagen" "Auslandsreisekrankenversicherung" "Covid-19 Zertifikate" "Geldbeutel: Personalausweis" "Geldbeutel: Geld" "Geldbeutel: Kreditkarte" "Geldbeutel: Versichertenkarte" "Geldbeutel: Führerschein")
Elektronik=("Handy" "Handyband" "Laptop samt Netzteil" "Kamera samt Netzteil" "Kopfhörer" "Kopfhöreradapter" "USB Netzteil + Ladekabel x 2" "Powerbank" "Selfiestick" "Switch" "Handyhüllen" "Wlan Handy" "HDMI Kabel" "Wifi USB Dongle" "Ladekabel Uhr")
Kleidung=("Lange Unterhose" "Schuhe: Hausschuhe" "Schuhe: Straßenschuhe" "Schuhe: Wanderschuhe" "Schuhe: Badelatschen" "Schuhe: Taucherschuhe" "Hemden" "Pullover" "Hose kurz x 2" "Hose lang" "Hose Jogginghose" "Hose Badehose" "Kappe/Mütze")
KleidungMultiple=("Unterhosen" "Socken" "T-Shirts")
Medikamente=("Dispenser" "L-Thyroxin" "Zusatz: Calcium" "Zusatz: Magnesium" "Schmerzmittel: Berlosin" "Schmerzmittel: Paracetamol" "Schmerzmittel: Ibuprofen" "Augengel" "Wundverband: Kompressen" "Wundverband: Verband" "Wundverband: Octenisept" "Pflaster & Blasenpflaster" "FFP2 Masken x 2" "Covid-19 Schnelltests x 2" "Sprunggelenkbandage")
Proviant=("Thermobecher" "Wasserflasche" "Obst: Äpfel" "Essensdosen" "Bonbons" "Zitronenteegranulat" "Messer" "Besteck")
Spezials=("Hochzeit: Geschenk" "Hochzeit: Kleidung" "Geburtstag: Geschenk")
Sonstiges=("Skihelm" "Skiunterwäsche" "Halstuch/Schal" "Regenschirm" "Jacke" "Buch" "Kissen" "Sonnenbrille" "Schlafmütze" "Ohrenstöpsel" "Tüten" "Taschentücher")

for i in $(seq 0 $((${#Bad[@]}-1))); do
	STUFF+=("Bad: ""${Bad[$i]}")
done
for i in $(seq 0 $((${#Dokumente[@]}-1))); do
	STUFF+=("Dokumente: ""${Dokumente[$i]}")
done
for i in $(seq 0 $((${#Elektronik[@]}-1))); do
	STUFF+=("Elektronik: ""${Elektronik[$i]}")
done
for i in $(seq 0 $((${#Kleidung[@]}-1))); do
	STUFF+=("Kleidung: ""${Kleidung[$i]}")
done
for i in $(seq 0 $((${#KleidungMultiple[@]}-1))); do
	STUFF+=("Kleidung: ""${KleidungMultiple[$i]}"" x ""$DAYS")
done
for i in $(seq 0 $((${#Medikamente[@]}-1))); do
	STUFF+=("Medis: ""${Medikamente[$i]}")
done
for i in $(seq 0 $((${#Proviant[@]}-1))); do
	STUFF+=("Proviant: ""${Proviant[$i]}")
done
for i in $(seq 0 $((${#Spezials[@]}-1))); do
	STUFF+=("Spezials: ""${Spezials[$i]}")
done
for i in $(seq 0 $((${#Sonstiges[@]}-1))); do
	STUFF+=("Sonstiges: ""${Sonstiges[$i]}")
done

NOW=$(date +%s)
echo "HEUTE WICHTIG<tab>""$TIMESTAMP""<tab>!Reise über ""$DAYS"" Tage<tab>" >> notDone_$NOW.txt
for i in $(seq 0 $((${#STUFF[@]}-1))); do
	echo "HEUTE WICHTIG<tab>""$(($TIMESTAMP+1))""<tab>""${STUFF[$i]}""<tab>" >> notDone_$NOW.txt
done
cat notDone_$NOW.txt | sort -h > notDone.txt
rm notDone_$NOW.txt
truncate -s-1 notDone.txt
cat notDone.txt

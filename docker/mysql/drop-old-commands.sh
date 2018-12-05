#!/usr/bin/env bash

if [ "$EUID" -ne 0 ]; then
    echo "Please run as root inside the mysql service"
    exit 1
fi;

# Export env vars
`cat /web/.env | sed 's/^\(.*\)$/export \1/g' | grep MYSQL_`

echo " [BACKUP] Starting backup for db $MYSQL_DATABASE - date : `date`"

filename="histologie-dump-`date +%Y-%m`.sql"
mysqldump -u "$MYSQL_USER" --password="$MYSQL_PASSWORD" "$MYSQL_DATABASE" > /web/bdd/backups/"$filename"

echo " [BACKUP] File saved in bdd/backups/$filename"

if [ "$#" -gt 0 ] && [[ $1 == --drop-commands ]]; then
    echo " [BACKUP] Script is about to delete the past commands"

    year=$((`date +%Y`-2))
    backupDir="/web/bdd/backups/$year"
    mkdir "$backupDir"

    # Saves the three tables into CSV file
    mysql -B -u "$MYSQL_USER" --database="$MYSQL_DATABASE" --password="$MYSQL_PASSWORD" \
        -e "SELECT c.idCommande, c.numCommande, c.dateCommande, c.dateReceptionCommande, c.dateRetourCommande, c.dateFacturationCommande, c.idUtilisateur, u.nomUtilisateur, u.prenomUtilisateur, u.mailUtilisateur, c.commentaireUtilisateur, c.commentairePlateau
            FROM Commande c, Utilisateur u
            WHERE YEAR(dateCommande) <= $year
              AND c.idUtilisateur = u.idUtilisateur
            ORDER BY c.numCommande" \
        | sed "s/'/\'/;s/\t/\";\"/g;s/^/\"/;s/$/\"/;s/\n//g" > "$backupDir/commandes-$year.csv"

    mysql -B -u "$MYSQL_USER" --database="$MYSQL_DATABASE" --password="$MYSQL_PASSWORD" \
        -e "SELECT e.idCommande, e.idEchantillon, e.numEchantillon, e.identAnimalEchantillon, e.dateInclusion, e.dateCoupe, o.nomOrgane, a.typeAnimal, i.nomInclusion, e.epaisseurCoupes, e.nbCoupes
            FROM Echantillon e, Organe o, Animal a, Inclusion i, Commande c
            WHERE e.idCommande = c.idCommande
              AND YEAR(c.dateCommande) <= $year
              AND e.idOrgane = o.idOrgane
              AND e.idAnimal = a.idAnimal
              AND e.idInclusion = i.idInclusion
            ORDER BY e.numEchantillon;" \
       | sed "s/'/\'/;s/\t/\";\"/g;s/^/\"/;s/$/\"/;s/\n//g" > "$backupDir/echantillons-$year.csv"


    mysql -u "$MYSQL_USER" --database="$MYSQL_DATABASE" --password="$MYSQL_PASSWORD" \
        --execute="DELETE FROM Commande WHERE YEAR(dateCommande) <= $year;"

    echo " [BACKUP] CSV files saved in $backupDir"
fi;

echo " [BACKUP] Done."

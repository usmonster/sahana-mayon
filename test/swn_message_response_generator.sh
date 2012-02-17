#!/bin/bash
#
# Send Word Now Message Response Generator
#
# Takes the passed event_id and executes a msql query to dump a csv file that can be opened in openoffice and
# converted to office 2003 .xls for consumption as a swn response.
#
# Note that the -n parameter gives an outfile pattern but all outfiles will be created relative to your pwd
#
# Usage: ./swn_response_generator.sh -h 127.0.0.1 -d mydatabase -n outfilepattern -u username -e 35 -l 20000

  
usage(){
  echo "This script queries a Mayon database and outputs a series of csv files formatted to look like Send Word Now Response Files
  
  Usage: ./swn_response_generator.sh -h 127.0.0.1 -d mydatabase -n outfilepattern -u username -e 35 -l 20000
  
  Parameters (Required):
      -d    Database name to query
      -e    Mayon event ID
      -h    Hostname or ip address of the database to query
      -l    The number of lines per-output file
      -n    The string pattern that will be used to generate output files (eg, pattern_output/pattern_aa.csv)
      -p    Prints this help statement
      -u    The database username"
  exit 1
}
  
HOST=
DB=
OUTPATTERN=
USER=
EVENT=
LINES=

while getopts "h:d:n:u:e:l:p" option
  do
    case "$option" in
      "h")
        HOST=$OPTARG
        ;;
      "d")
        DB=$OPTARG
        ;;
      "n")
        OUTPATTERN=$OPTARG
        ;;
      "u")
        USER=$OPTARG
        ;;
      "e")
        EVENT=$OPTARG
        ;;
      "l")
        LINES=$OPTARG
        ;;
      "p")
        usage
        exit 1
        ;;
      "?")
        echo "Invalid option: -$OPTARG" >&2
        exit 1
       ;;
      ":")
        echo "Option -$OPTARG requires an argument" >&2
        exit 1
       ;;
      *)
        echo "Unkown error processing options" >&2
        exit 1
        ;;
    esac
  done

OUTFILE="/tmp/${OUTPATTERN}"
HEADFILE="/tmp/${OUTPATTERN}_header.csv"
CONTENTFILE="/tmp/${OUTPATTERN}_content.csv"
OUTDIR="./${OUTPATTERN}_output"

QUERY="SELECT sr.id AS 'Contact ID',
    'Bestertester, Fester' AS 'Name',
    ect.email_contact_type AS 'Type',
    ec.email_contact AS 'Contact Point',
    CASE MOD((es.id + eec.id),3) WHEN 0 THEN 'Possibly Dead' WHEN 1 THEN 'Not Dead Yet' WHEN 2 THEN 'Martian Spider' END AS 'Status',
    SUBSTR(DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %T.%f'),1,23) AS 'Sent', 
    SUBSTR(DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %T.%f'),1,23) AS 'Delivered', 
    CASE MOD((es.id + eec.id),3) WHEN 0 THEN '' WHEN 1 THEN 1 WHEN 2 THEN 2 END AS 'Response'
  FROM ag_event_staff es
    INNER JOIN ag_staff_resource sr ON es.staff_resource_id = sr.id 
    INNER JOIN ag_staff s ON sr.staff_id = s.id 
    INNER JOIN ag_person p ON s.person_id = p.id 
    INNER JOIN ag_entity e ON p.entity_id = e.id 
    INNER JOIN ag_entity_email_contact eec ON e.id = eec.entity_id 
    INNER JOIN ag_email_contact_type ect ON eec.email_contact_type_id = ect.id 
    INNER JOIN ag_email_contact ec ON eec.email_contact_id = ec.id
  WHERE event_id = '$EVENT'
  
  UNION ALL SELECT sr.id AS 'Contact ID',
    'Bestertester, Fester' AS 'Name',
    pct.phone_contact_type AS 'Type',
    pc.phone_contact AS 'Contact Point',
    CASE MOD((es.id + epc.id),3) WHEN 0 THEN 'Possibly Dead' WHEN 1 THEN 'Not Dead Yet' WHEN 2 THEN 'Martian Spider' END AS 'Status',
    SUBSTR(DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %T.%f'),1,23) AS 'Sent', 
    SUBSTR(DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%d %T.%f'),1,23) AS 'Delivered', 
    CASE MOD((es.id + epc.id),3) WHEN 0 THEN '' WHEN 1 THEN 1 WHEN 2 THEN 2 END AS 'Response'
  FROM ag_event_staff es
    INNER JOIN ag_staff_resource sr ON es.staff_resource_id = sr.id 
    INNER JOIN ag_staff s ON sr.staff_id = s.id 
    INNER JOIN ag_person p ON s.person_id = p.id 
    INNER JOIN ag_entity e ON p.entity_id = e.id 
    INNER JOIN ag_entity_phone_contact epc ON e.id = epc.entity_id 
    INNER JOIN ag_phone_contact_type pct ON epc.phone_contact_type_id = pct.id 
    INNER JOIN ag_phone_contact pc ON epc.phone_contact_id = pc.id 
  WHERE event_id = '$EVENT';"

# execute the query and output to a csv
echo "Executing query for event \`$EVENT\` against db \`$DB\` on host \`$HOST\` as user \`$USER\`..."
mysql -B -u "$USER" -p -h "$HOST" -e "$QUERY" "$DB" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > "$OUTFILE"

# loop through the csv and split it to make it manageable for things like office
if [ -d "$OUTDIR" ]; then
  rm -rf "$OUTDIR"
fi
mkdir "$OUTDIR"
head -n 1 "${OUTFILE}" > "${HEADFILE}"
tail -n +2 "${OUTFILE}" > "${CONTENTFILE}"
split -l $LINES "${CONTENTFILE}" "${OUTDIR}/${OUTPATTERN}_"
unlink "${CONTENTFILE}"

for f in $(find "$OUTDIR" -type f); do cat "${HEADFILE}" $f > $f.csv ; unlink $f; done;
unlink "$HEADFILE"
unlink "$OUTFILE"

echo "Successfully output query to ${OUTPATTERN}_output."

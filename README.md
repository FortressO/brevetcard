Brevet cards are witness cards that a Randonneur uses to record participation in a Brevet.
 Ride organizers fill in the name of the event, the riders name and the location of each
"control" where a rider asks a witness to fill in a time of passage and an initial.
This set of scripts allows some of the repetitive tasks of the organizer to be automated.
Gps files that specify the Route inherently contain the precise distance of each control
as well as cues that specify the name of the control.

control_time.php retrieves the control distance from gps files and formats the fields of a brevet 

post_brevet_card.php is designed to recieve the pre-formated strings as well as an array of control
information and an array of rider names. It makes some adjustementof the font sizes and may truncate 
long strings in order to avoid over printing other parts of the form. The down loaded pdf file 
contains a brevet card for each name found in the riderlist array.



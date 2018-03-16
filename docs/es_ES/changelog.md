UPDATE
===

> <span style="color:red">**UPDATE**</span>
>
> Telejee passe en version 2 ce qui otpimise fortement le plugin. Vos configurations ne seront pas perdues.
> 
> Aprés avoir effectué la mise à jour , si ce n'est déjà fait , il faut aller dans la configuration de chaque chaîne et choisir le bouquet. Vous n'aurez qu'à faire ça qu'une fois.
>
> Le widget est revu complétement et fini la configuration des colonnes. Si la taille ne joue pas sur le dashboard/design, il faut redimensionner le widget en mode modification.
>
> Bien lire le changelog car il y a des nouveautés (Widget , commandes , télécommandes...).
>
> En cas de problème n'hésitez pas à me le signaler sur le forum [cliquer ici](https://www.jeedom.com/forum/viewtopic.php?f=142&t=6111)
>
> Merci pour votre compréhension

12.03.2018
===

* Correction des dépendances pour PHP7
* Possibilité de changer l'ordre des commandes dans la configuration des télécommandes (Pas sur le widget)
* Correction du bug pour la touche 5 des télécommandes


25.02.2018
===

* Otimisation
* Ajouts d'icône (Il faut enregistrer les chaînes concernées)

25.02.2018
===

* Otimisation
* Ajouts d'icône (Il faut enregistrer les chaînes concernées)

17.02.2018
===

* Ajout d'un bouton allociné pour être redirigé vers la page de recherche du programme
* Ajout du nom du programme dans la description du programme
* Correction du bug d'affichage des émissions du moment

01.2018
===

* Menu fixe pour le widget des programmes
* Ajout d'une action de fin lors de la recherche d'émission
* Ajout de la commande "A venir" permettant de connaître le programme suivant le programme en cours
* Possibilité de choisir les mots clés via un menu
* Ajout d'une nouvelle télécommande
* Commandes visibles pour les chaînes pour les tester
* Correction pour la zone horaire
* Amelioration de la fonction recherche
* Possibilité de choisir l'ordre d'affichage sur le widget
* Correction concernant 2 bouquet (Telesat et bouyghes)
* Doc
* Optimisation du code

29.12.2017
===

* La durée pour l'éxécution des actions de la recherche selon la durée de l'émission. Cela doit éviter les répétitions
* Correction des erreurs 
* Correction du bug sur le bouquet TNT

26.12.2017
===

* Widget
* Installation de dépendance pour essayer d'installer mb_string si besoin
* Ajout d'icônes
* Amélioration de la recherche d'une émission ( Caractères spéciaux,Majuscule)

26.09.2017
=== 

* Fix command.
* Doc.
* Icônes.



18.09.207
===

Correction pour l'ordre d'affichage des chaînes sur le widget et dans la configuration. 
Ajouts des mots clé pour les actions lors de la recherche.
Ajout de bouquets: Bouygues, telesat,sfr Belgique
Possibilité de chosir des chaînes sur différents bouquets
Barre de recherche pour la création des chaines

15.09.2017

Protection du nom des chaînes , correction de l'affichage en colonnes , Ajout d'informations dans la fenêtre d'un programme.

11.09.2017

Ajout icones, taille fixe pour l'affichage des chaînes?

10.07.2017

 Corrections du bug des horaires.

12.06.2017

 Corrections du bug d'affichage.changelog

17.05.2017

 Correction du widget pour les vues

13.05.207

 Ajout des programmes de fin de soirés dans les commandes d'info

7.05.207

 Possibilité de créer des commandes pour commander les chaînes via scénarios ou interactions , connaître les programmes du moment ou du soir sur les différentes chaînes.(voir doc)

3.05.207

 Possibilité de choisir le nombre de colonne(s) à afficher dans le widget . Ajout du bouton supprimer pour les chaînes.

2.05.2017

 Ajouts d'icônes

27.04.2017

 Corrections du bug d'affichage et doc. Attendre au moins 5 minutes pour la mise à jour des programmes actuels

25.04.2017

 Ajout d'une télécommande (utiliser la configuration avancée pour le fond et les bords arrondis si besoin). Optimisation du code

12.04.2017

 Correction du bug d'affichage. Changement icônes

6.04.2017

 Charte graphique. Possibilité de créer des télécommandes (voir doc : 2 pour le moment mais en réfléxion et si besoin n'hésitez pas à faire la demande sur le forum). Pas besoin de réinstaller aprés installation. Mise à jour de la la doc.

1.0

 Passage en version stable

0.997

 Correction des erreurs présentes dans les logs et des liens vers les resumes des programmes du moment.

0.996

 Correction des erreurs présentes dans les logs

0.995

 Correction du problème avec la tnt

0.994

 Correction d'erreur sur la release précédente

0.993

 Correction de l'affichage du programme du moment

0.992

 Mise à jour et programme pour le moment OK. Suppression de la roue crantée sur le dashboard.

0.991

 Changement dans l'ordre des onglets.

0.99

 Gestion des mots clés(sleep, variable, scenario, stop, icon, say,wait,return,gotodesign,log,message) dans le choix des actions sur les logos ou le bouton play 

0.98

 Possibilité de choisir la catégorie( Permet de choisir la couleur de fond du widget). Effet de slide lors du changement de programme

0.97

 Corrige le bug de lancement des actions "En ce moment"

0.963

 Corrige le bug lors de l'effacement d'un équipement.

0.961

 Correction du bug lors de la suppression d'un équipement ( N'essayez pas , mettez à jour :) )

0.96

Pour plus de facilité d'utilisation nouvelle version avec rajout d'un bouton pour mettre à jour les icônes si changement de pack d'icônes.

0.95

 En dessous de le version 0.9 , il faut tout réinstaller sinon juste faire la mise à jour. CLiquer sur sauvegarder pour valider le choix du pack d'icônes aprés installation.Possibilité de choisir la taille du widget(dans l'équipement configuration.Possibilité de choisir un pack d'icône (Merci à SBO). Pour chaque chaîne possibilité de rechercher si un programme est en cours de diffusion.

0.92

Bugs corrigée concernant l'affichage dans l'onglet 'en ce moment'. Ajout d'icone. Amelioration du code

0.9

 Refonte complète. Possibilité d'avoir plus de 120 chaines et de choisir votre bouquet. Avant toute installation il faut desinstaller la version qui est sur votre jeedom. Puis vous installer la mise à jour , choisissez votre bouquet et puis dans le plugin vous cliquez sur charger le plugin. Ensuite vous pouvez ajouter les chaînes dans l'ordre que vous voulez.

0.6

 Correction du problème lors de la création. Maintenant pour toutes installations inférieures à la 0.5 , il suffit de cliquer sur le bouton ajouter les chaînes. Plus de problème dans la vue designe pour changer d'onglet. Corrections de quelques fôtes d'orthographe

0.5

 Attention , il faut effacer la version précédente avant d'installer celle-ci.Refonte complète du plugin avec facilité de configuration pour les commandes, possibilité d'obtenir des informations  sur les programmes en cliquant sur l'image

0.3

 Mise à jour de la doc. Changement de la taille du widget. Ajout d'un bouton "play" pour lancer des actions prédéfinies

0.2

 Possibilité de lancer des actions au clic sur le logo de la chaine

0.11

 Regle le problème d'affichage dans le design.Améliorations du code. Doc.

0.1

 Mise à disposition du plugin sur le market

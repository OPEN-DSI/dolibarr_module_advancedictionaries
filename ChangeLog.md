# Changelog
Le format du fichier est basé sur [Tenez un ChangeLog](http://keepachangelog.com/fr/1.0.0/).

## [Non Distribué]

## [4.0.59] - 22-09-2023
- Changement de marque Open-DSI > Easya Solutions

## [4.0.58] - 03-07-2023
- Ajout de paramètres pour compléter le filtre WHERE et le HAVING dans la récupération des lignes

## [4.0.57] - 08-06-2023
- Ajout d'une nouvelle option de champ 'empty_options'

## [4.0.56] - 10-03-2023
- correction de l'enregistrement des balise HTML des type text avec WYSIWYG

## [4.0.55] - 03-02-2022
- correction de l'affichage de AdvanceDictionary dans le menu setup

## [4.0.54] - 19-12-2022
- Compatibilité newToken() des versions recentes de dolibarr

## [4.0.53] - 29-11-2022
- Compatibilité PHP8.1
- Compatibilité Dolibarr v17-beta

## [4.0.52] - 14-09-2022
- Correction de l'affichage de l'entrée dans le menu 'configuration'

## [4.0.51] - 21-07-2022
- Correction de la mise à jour d'un champ du dictionnaire lors d'une montée de version du dictionnaire

## [4.0.50] - 12-07-2022
- Correction de l'enregistrement d'une ligne lorsqu'il n'y a qu'un champ à multiple selection à mettre à jour

## [4.0.49] - 11-05-2022
- Correction de l'affichage de l'entrée dans le menu 'configuration'

## [4.0.48] - 11-05-2022
- Compatibilité dolibarr v15

## [4.0.47] - 08-05-2022
- Compatibilité dolibarr v15

## [4.0.46] - 27-01-2022
- Correction lors de la création du dictionnaire pour la premiere fois lorsqu'un champ à disparue lors d'une de ses versions

## [4.0.45] - 14-01-2022
- Correction de la mise à jour d'une table déjà existante à la création du dictionnaire
- Correction de l'emplacement d'execution de la hook 'formConfirm'

## [4.0.44] - 15-12-2021
- Ajout l'accès au web browser pour sélectionner les fichiers documents depuis le bouton image d'un champ de type text

## [4.0.43] - 29-10-2021
- Ignorer les "erreurs" (table/colonne existe deja, index supprimé n'existe pas, ...) non bloquante lors de la creation/maj des dictionnaires

## [4.0.42] - 28-09-2021
- Passe les colonnes non définies lors de la creation
- Corrections mise à jour de la version du dictionnaire sur l'entité 0 et non l'entité actuelle (avait disparue entre temps)

## [4.0.41] - 27-09-2021
- Ajout la gestion de l'update d'une colonne de type sellist vers chkbxlst (rajoute les valeurs existantes dans la table de liaison et supprime la colonne)

## [4.0.40] - 22-07-2021
- Corrections mises à jour de la version du dictionnaire sur l'entité 0 et non l'entité actuelle
- Remise des fonctions isLineCanBeUpdated() et isLineCanBeDeleted()
- Ajout du type de champ 'chkbxlstwithorder' par AlexisLaurier
- Ajout de l'option 'association_table' par AlexisLaurier
- Corrections

## [4.0.39] - 06-07-2021
- Ajout des propriétés 'hideTitleBlock' et 'listTitle' sur le dictionnaire
- Ajout fonction getFixedParameters() pour des paramètres fixes passés en Query
- Correction du format des valeurs des lignes lues
- Correction de l'affichage du tri par défaut
- Mise en standard des recherches de dates (filtre date : array('key' => array('date_start' => int, 'date_end' => int)))

## [4.0.38] - 22-06-2021
- Compatibilité de l'API avec les versions 12+ de Dolibarr

## [4.0.37] - 28-04-2021
- Correction de l'auto incrementation de la clé primaire lors de la creation d'une table

## [4.0.36] - 28-04-2021
- Ajout de la posibilité de déterminé la clé primaire
- Ajout mechanique de mise a jour de la clé primaire
- Correction mineures

## [4.0.35] - 28-04-2021
- Correction mineure

## [4.0.34] - 31-03-2021
- Correction lors de la création du dictionnaire (il n'applique plus les mises à jour de monter de version)

## [4.0.33] - 09-03-2021
- Problème d'affichage icône fontawesome
- Ajout d'un lien vers les dictionnaires avancés directement depuis le menu configuration
- ChangeLog
- Ajout d'un gitignore

## [4.0.32] - 04-12-2020
- Ajout de l'option sur les champs : 'default_value'

## [4.0.31] - 23-11-2020
- Correction compatibilité avec postgresql lors de l'activation du module.

## [4.0.30] - 18-11-2020
- Ajout compatibilité avec postgresql. 

## [4.0.29] - 13-11-2020
- Correction mineure pour l'affichage du message de "Accès refusé" si le dictionnaire n'est pas activé.
- Ajout de la possibilité d'autoriser ou non la mise a jour ou la suppression sur chaque ligne par code.

## [4.0.28] - 09-11-2020
- Correction de la detection de la version du dictionnaire installer en multisociete

## [4.0.27] - 16-09-2020
- Correction mineure pour la fonction showUpdateListValuesScript()
- Correction instruction group by avec le type chkbxlst

## [4.0.26] - 15-09-2020
- Correction instruction group by avec le type chkbxlst

## [4.0.25] - 11-09-2020
- Ajout de l'option 'show_entity_management' pour afficher la gestion des entités pour les lignes du dictionnaire (affiche la colonne 'Environement' et l'action en masse de modifier l'entité des lignes)
- Correction du test des valeurs maximal et minimal

## [4.0.24] - 10-09-2020
- Correction du test des valeurs maximal et minimal lors de la modification/creation d'une ligne
- Correction instruction group by

## [4.0.23] - 09-09-2020
- Correction filtre entité

## [4.0.22] - 02-09-2020
- Ajout de la possibilité de modifier l'entité des lignes de dictionnaire en masse (les lignes sur l'entité 0 et entité maitre seront considérées comme communes à toutes les entités)

## [4.0.21] - 16-07-2020
- Correction lors de l'insertion de nouvelles lignes (valeur NULL)

## [4.0.20] - 26-06-2020
- Ajout du nom de la table lors d'une erreur lors de la creation des dictionnaires

## [4.0.19] - 24-06-2020
- Compatibilité v12

## [4.0.18] - 06-06-2020
- Ajout du rafraîchissement des valeurs d'une liste à la modification d'un champ par AJAX (nouveau paramètre: ajax_options)
- Amélioration du paramètre 'options' pour les champs de type 'sellist' et 'chkbxlst'

## [4.0.17] - 29-04-2020
- Ajout d'un 8ème paramètre (nom du champ contient le fichier de langue a charger pour la traduction du label) dans les options du type de champ 'sellist' et 'chkbxlst'

## [4.0.16] - 02-03-2020
- Corrections test sur les erreurs de duplication

## [4.0.15] - 14-02-2020
- Corrections diverses (fixe les warnings)

## [4.0.14] - 11-02-2020
- Correction de l'affichage de l'icône de titre ('titlePicto'), du titre ('customTitle') et du lien de retour ('customBackLink') personnalisés
- Ajout d'une nouvelle propriété pour cache le lien de retour : 'hideCustomBackLink'
- Ajout d'une fonction 'doActions' dans la classe dictionnaire executée dans le fichier 'core/actions_dictionaries.inc.php'
- Ajout des options sur les champs : 'unselected_values', 'translate_prefix', 'translate_suffix'
- Ajout de l'option pour voir l'id de la ligne
- Ajout d'une option sur l'id est un code
- Correction et amélioration diverses

## [4.0.13] - 28-10-2019
### Ajouter
- Ajout du type de champ 'float'
- Ajout des 2 options de champ 'label_in_add_edit', 'add_params_in_add_edit', 'is_not_show_in_add' et 'is_not_show_in_edit'.
- Correction de l'appel ajax de la liste des lines du dictionnaire

## [4.0.12] - 09-09-2019
### Ajouter
- Prise en charge des options 'is_not_show', 'is_not_addable', 'is_not_editable' et 'is_not_sortable'.
- Correction de la récupération des valeurs de champs de type 'price' et 'double' avec une virgule.

## [4.0.11] - 26-07-2019
### Ajouter
- Corrections mineures.

## [4.0.10] - 24-06-2019
### Ajouter
- Affichage d'une fenêtre pour l'edition et la modification d'une ligne
- Ajout de l'affichage de la liste des lignes au standard Dolibarr (support QuickList)

## [4.0.9] - 28-03-2019
### Ajouter
- Modifie le type de donnée en base des types 'select', 'sellist' et 'radio' de text en varchar(255)
- Attention !!! Changement de 4 fonctions pour éviter les warnings: showOutputField, showOutputCustomField, showInputField, showInputCustomField => showOutputFieldAD, showOutputCustomFieldAD, showInputFieldAD, showInputCustomFieldAD
  Les modules non modifiés en conséquences ne seront plus compatibles

## [4.0.8] - 13-03-2019
### Ajouter
- Correction sur l'appel d'une hook de fetch_lines si appelé depuis une hook.
- Ajout include manquantes
- Ajout de l'option sur le select_dictionary et select_dictionary_lines, si le filter est null ne retourne aucunes lignes

## [4.0.7] - 07-12-2018
### Ajouter
- Correction sur les filtres textes sur les champs de type 'chkbxlst'.
- Modification du nom des hook de la fonction 'fetch_lines' de la classe 'Dictionary'
- Correction d'affichage si pas de valeurs sélectionnées pour les champs de type 'checkbox'.
- Les filtres des listes sont gardé même lors d'un ajout ou d'une modification.

## [4.0.6] - 16-11-2018
### Ajouter
- Ajout de l'API pour la gestion des dictionnaires.
- Corrections mineures.

## [4.0.5] - 24-09-2018
### Ajouter
- Corrections mineures des clés étrangères + affichage d'un message si l'on essaye de supprimer une valeur utilisé.
- Option td_output: positionLine in field definition.
- Option td_output: colspan in field definition.

## [4.0.4] - 24-09-2018
### Ajouter
- Corrections mineures sur le bouton d'aide.
- Modification des fonctions formulaires de selection de valeurs d'un dictionnaire (fichier: html.formdictionary.class.php)

## [4.0.3] - 24-09-2018
### Ajouter
- Corrections mineures.

## [4.0.2] - 14-09-2018
### Ajouter
- Correction mineures.

## [4.0.1] - 13-08-2018
### Ajouter
- Update dictionary management.
- Option td_input: colspan in field definition.
- Option label_separator in field definition.

## [4.0.0] - 16-07-2018
- Version initial.

[Non Distribué]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/compare/v4.0.59...HEAD
[4.0.59]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.59
[4.0.58]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.58
[4.0.57]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.57
[4.0.56]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.56
[4.0.54]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.54
[4.0.53]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.53
[4.0.52]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.52
[4.0.51]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.51
[4.0.50]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.50
[4.0.49]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.49
[4.0.48]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.48
[4.0.47]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.47
[4.0.46]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.46
[4.0.45]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.45
[4.0.44]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.44
[4.0.43]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.43
[4.0.42]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.42
[4.0.41]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.41
[4.0.40]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.40
[4.0.39]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.39
[4.0.38]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.38
[4.0.37]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.37
[4.0.36]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.36
[4.0.35]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.35
[4.0.34]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.34
[4.0.33]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.33
[4.0.32]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.32
[4.0.31]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.31
[4.0.29]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.29
[4.0.28]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.28
[4.0.27]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.27
[4.0.26]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.26
[4.0.25]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.25
[4.0.24]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.24
[4.0.23]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.23
[4.0.22]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.22
[4.0.21]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.21
[4.0.20]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.20
[4.0.19]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.19
[4.0.18]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.18
[4.0.17]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.17
[4.0.16]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.16
[4.0.14]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.14
[4.0.13]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/commits/v4.0.13
[4.0.12]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.12
[4.0.11]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.11
[4.0.10]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.10
[4.0.9]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.9
[4.0.8]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.8
[4.0.7]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.7
[4.0.4]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.4
[4.0.3]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.3
[4.0.2]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.2
[4.0.1]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.1
[4.0.0]: http://git.open-dsi.fr/dolibarr-extension/advancedictionaries/commits/v4.0.0

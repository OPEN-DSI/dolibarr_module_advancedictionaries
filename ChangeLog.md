# Changelog
Le format du fichier est basé sur [Tenez un ChangeLog](http://keepachangelog.com/fr/1.0.0/).

## [Non Distribué]

## [4.0.21] - 16-07-2020
- Correction lors de l'insertion de nouvelles lignes (valeur NULL)

## [4.0.20] - 26-06-2020
- Ajout du du nom de la table lors d'une erreur lors de la creation des dictionnaires

## [4.0.19] - 24-06-2020
- Compatibilité v12

## [4.0.18] - 06-06-2020
- Ajout du raffraichissement des valeurs d'une liste à la modification d'un champ par AJAX (nouveau parametre: ajax_options)
- Amélioration du parametre 'options' pour les champs de type 'sellist' et 'chkbxlst'

## [4.0.17] - 29-04-2020
- Ajout d'un 8eme paramètre (nom du champ content le fichier de langue a chager pour la traduction du label) dans les options du type de champ 'sellist' et 'chkbxlst'

## [4.0.16] - 02-03-2020
- Corrections test sur les erreurs de duplication

## [4.0.15] - 14-02-2020
- Corrections diverses (fixe les warnings)

## [4.0.14] - 11-02-2020
- Correction de l'affichage de l'icone de titre ('titlePicto'), du titre ('customTitle') et du lien de retour ('customBackLink') personnalisés
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
- Correction mineures.

## [4.0.10] - 24-06-2019
### Ajouter
- Affichage d'une fenetre pour l'edition et la modification d'une ligne
- Ajout de l'affichage de la liste des lignes au standard Dolibarr (support QuickList)

## [4.0.9] - 28-03-2019
### Ajouter
- Modifie le type de donnée en base des types 'select', 'sellist' et 'radio' de text en varchar(255)
- Attention !!! Changement de 4 fonctions pour éviter les warnings: showOutputField, showOutputCustomField, showInputField, showInputCustomField => showOutputFieldAD, showOutputCustomFieldAD, showInputFieldAD, showInputCustomFieldAD
  Les modules non modifiés en conséquances ne seront plus compatibles

## [4.0.8] - 13-03-2019
### Ajouter
- Correction sur l'appel d'une hook de fetch_lines si appellé depuis une hook.
- Ajout include manquantes
- Ajout de l'option sur le select_dictionary et select_dictionary_lines, si le filter est null ne retourne aucunes lignes

## [4.0.7] - 07-12-2018
### Ajouter
- Correction sur les filtres texte sur les champs de type 'chkbxlst'.
- Modification du nom des hook de la fonction 'fetch_lines' de la classe 'Dictionary'
- Correction d'affichage si pas de valeurs selectionné pour les champs de type 'checkbox'.
- Les filtres des listes sont gardé même lors d'un ajout ou d'une modification.

## [4.0.6] - 16-11-2018
### Ajouter
- Ajout de l'API pour la gestion des dictionnaires.
- Correction mineures.

## [4.0.5] - 24-09-2018
### Ajouter
- Correction mineures des clés étrangères + affichage d'un message si l'on essaye de supprimer une valeur utilisé.
- Option td_output: positionLine in field definition.
- Option td_output: colspan in field definition.

## [4.0.4] - 24-09-2018
### Ajouter
- Correction mineures sur le bouton d'aide.
- Modification des fonctions formulaires de selection de valeurs d'un dicto (fichier: html.formdictionary.class.php)

## [4.0.3] - 24-09-2018
### Ajouter
- Correction mineures.

## [4.0.2] - 14-09-2018
### Ajouter
- Correction mineures.

## [4.0.1] - 13-08-2018
### Ajouter
- Update dictionary managment.
- Option td_input: colspan in field definition.
- Option label_separator in field definition.

## [4.0.0] - 16-07-2018
- Version initial.

[Non Distribué]: https://github.com/OPEN-DSI/dolibarr_module_advancedictionaries/compare/v4.0.21...HEAD
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

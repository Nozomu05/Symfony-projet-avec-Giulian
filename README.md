# 📚 API Bibliothèque - Guide de Test

**Membres du groupe :** TRAN Minh Hoang Anh et PERRIN Giulian

---

## 🚀 Prérequis

- Serveur Symfony démarré : `symfony server:start`
- Base URL : `http://127.0.0.1:8000`
- Outil de test : Postman, Insomnia, ou REST Client

---

## 📋 Tests de l'API (Dans l'ordre)

### **1️⃣ Création d'un utilisateur**

```http
POST http://127.0.0.1:8000/api/utilisateurs
Content-Type: application/json

{
  "nom": "Dupont",
  "prenom": "Jean"
}
```

**Réponse attendue :** `201 Created`

---

### **2️⃣ Vérification de la création de l'utilisateur**

```http
GET http://127.0.0.1:8000/api/utilisateurs
```

**Réponse attendue :** `200 OK` avec la liste des utilisateurs

---

### **3️⃣ Création des livres**

#### Livre 1 : 1984
```http
POST http://127.0.0.1:8000/api/livres
Content-Type: application/json

{
  "titre": "1984",
  "auteur_nom": "Orwell",
  "auteur_prenom": "George",
  "auteur_biographie": "Écrivain britannique célèbre pour ses romans dystopiques",
  "auteur_date_naissance": "1903-06-25",
  "categorie_nom": "Dystopie",
  "categorie_description": "Romans présentant des sociétés totalitaires",
  "date_publication": "1949-06-08",
  "disponible": true
}
```

#### Livre 2 : Fondation
```http
POST http://127.0.0.1:8000/api/livres
Content-Type: application/json

{
  "titre": "Fondation",
  "auteur_nom": "Asimov",
  "auteur_prenom": "Isaac",
  "auteur_biographie": "Écrivain américain de science-fiction",
  "auteur_date_naissance": "1920-01-02",
  "categorie_nom": "Science-Fiction",
  "categorie_description": "Romans de science-fiction et d'anticipation",
  "date_publication": "1951-05-01",
  "disponible": true
}
```

**Réponse attendue :** `201 Created` pour chaque livre

---

### **4️⃣ Voir tous les livres**

```http
GET http://127.0.0.1:8000/api/livres
```

**Réponse attendue :** `200 OK` avec la liste des livres

---

### **5️⃣ Voir le livre d'ID 1**

```http
GET http://127.0.0.1:8000/api/livres/1
```

**Réponse attendue :** `200 OK` avec les détails du livre

---

### **6️⃣ Créer des emprunts**

#### Emprunt 1 : Jean Dupont emprunte 1984
```http
POST http://127.0.0.1:8000/api/emprunts/emprunter
Content-Type: application/json

{
  "utilisateur_nom": "Dupont",
  "utilisateur_prenom": "Jean",
  "livre_titre": "1984"
}
```

#### Emprunt 2 : Jean Dupont emprunte Fondation
```http
POST http://127.0.0.1:8000/api/emprunts/emprunter
Content-Type: application/json

{
  "utilisateur_nom": "Dupont",
  "utilisateur_prenom": "Jean",
  "livre_titre": "Fondation"
}
```

**Réponse attendue :** `201 Created` pour chaque emprunt

---

### **7️⃣ Voir tous les emprunts**

```http
GET http://127.0.0.1:8000/api/emprunts
```

**Réponse attendue :** `200 OK` avec la liste de tous les emprunts

---

### **8️⃣ Rendre un livre**

```http
PUT http://127.0.0.1:8000/api/emprunts/rendre/1
```

**Réponse attendue :** `200 OK` avec confirmation du retour

---

### **9️⃣ Voir tous les emprunts en cours**

```http
GET http://127.0.0.1:8000/api/emprunts/en-cours
```

**Réponse attendue :** `200 OK` avec uniquement les emprunts non rendus

---

### **🔟 Voir les emprunts d'un utilisateur spécifique**

```http
GET http://127.0.0.1:8000/api/emprunts/utilisateur/1
```

**Réponse attendue :** `200 OK` avec :
- Nombre d'emprunts en cours
- Nombre de livres restants disponibles (max 4)
- Liste des emprunts de l'utilisateur

---

### **1️⃣1️⃣ Modification d'un livre**

```http
PUT http://127.0.0.1:8000/api/livres/1
Content-Type: application/json

{
  "titre": "1984 - Édition Spéciale"
}
```

**Réponse attendue :** `200 OK` avec les données mises à jour

---

### **1️⃣2️⃣ Supprimer un livre**

```http
DELETE http://127.0.0.1:8000/api/livres/1
```

**Réponse attendue :** `200 OK` avec message de confirmation

---

### **1️⃣3️⃣ Vérifier si un livre est bien supprimé**

```http
GET http://127.0.0.1:8000/api/livres/1
```

**Réponse attendue :** `404 Not Found`

---

## 📊 Récapitulatif des Endpoints

### **👤 Utilisateurs**
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `POST` | `/api/utilisateurs` | Créer un utilisateur |
| `GET` | `/api/utilisateurs` | Lister tous les utilisateurs |
| `GET` | `/api/utilisateurs/{id}` | Voir un utilisateur spécifique |

### **📚 Livres**
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `POST` | `/api/livres` | Créer un livre |
| `GET` | `/api/livres` | Lister tous les livres |
| `GET` | `/api/livres/{id}` | Voir un livre spécifique |
| `PUT/PATCH` | `/api/livres/{id}` | Modifier un livre |
| `DELETE` | `/api/livres/{id}` | Supprimer un livre |

### **📖 Emprunts**
| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `POST` | `/api/emprunts/emprunter` | Emprunter un livre |
| `PUT/PATCH` | `/api/emprunts/rendre/{id}` | Rendre un livre |
| `GET` | `/api/emprunts` | Lister tous les emprunts |
| `GET` | `/api/emprunts/en-cours` | Lister les emprunts en cours |
| `GET` | `/api/emprunts/utilisateur/{id}` | Voir les emprunts d'un utilisateur |

---

## 🎯 Codes de Réponse HTTP

| Code | Signification | Utilisation |
|------|---------------|-------------|
| `200 OK` | Succès | GET, PUT, DELETE réussis |
| `201 Created` | Créé | POST réussi |
| `400 Bad Request` | Données invalides | Champs manquants, limite atteinte |
| `404 Not Found` | Non trouvé | Ressource inexistante |
| `409 Conflict` | Conflit | Doublon, livre déjà emprunté |

---

## ✅ Règles Métier

- ✅ Un utilisateur peut emprunter **maximum 4 livres** en même temps
- ✅ Un livre ne peut être emprunté que par **une seule personne** à la fois
- ✅ Un livre est considéré emprunté si `dateRetour` est `null`
- ✅ Création automatique des **auteurs** et **catégories** lors de l'ajout d'un livre
- ✅ Vérification des **doublons** pour les utilisateurs (nom + prénom)

---

## 🚀 Démarrage rapide

```bash
# 1. Installer les dépendances
composer install

# 2. Configurer la base de données (.env)
DATABASE_URL="mysql://root:@127.0.0.1:3306/bibliotheque"

# 3. Créer la base de données
php bin/console doctrine:database:create

# 4. Exécuter les migrations
php bin/console doctrine:migrations:migrate

# 5. Démarrer le serveur
symfony server:start
```

---

## 📦 Import dans Postman

Vous pouvez copier-coller les requêtes HTTP directement dans Postman ou créer une collection en important ce README.

**Astuce :** Utilisez l'extension **REST Client** dans VS Code pour tester directement depuis votre éditeur !

---

**🎉 Votre API est prête à être testée !**

import os
if __name__ == "__main__":
    try:
        os.system("gcc -o solveur solveur.c")
        # Pour s'assurer des droits d'exécution
        # os.system("chmod +x solveur")
        print("Le solveur a bien pu être compilé.")
    except Exception as e:
        print(f"Le solveur n'a pas pu être compilé.\n{e}")
        print("""Allez sur : https://jmeubank.github.io/tdm-gcc/
    Téléchargez l'installateur (TDM-GCC x64).
    Installez-le avec les options par défaut.
    Une fois installé, redémarrez votre terminal (ou ouvrez cmd.exe) et tapez `gcc --version`""")
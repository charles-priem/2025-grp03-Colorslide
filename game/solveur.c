#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <math.h>

#define EMPTY -1
#define VISITED 0
#define PATH 1
#define WALL 2
#define TP 3
#define HOLE 4

int playground[8][10] = {
    {EMPTY, EMPTY, EMPTY, WALL, WALL, WALL, WALL, WALL, WALL, EMPTY}, 
    {EMPTY, EMPTY, EMPTY, WALL, TP,   PATH, PATH, PATH, WALL, EMPTY}, 
    {WALL,  WALL,  WALL,  WALL, PATH, WALL, WALL, PATH, WALL, EMPTY}, 
    {WALL,  PATH,  PATH,  PATH, PATH, WALL, WALL, PATH, WALL, EMPTY}, 
    {WALL,  WALL,  WALL,  WALL, PATH, WALL, WALL, PATH, WALL, EMPTY}, 
    {EMPTY, EMPTY, EMPTY, WALL, PATH, WALL, WALL, PATH, WALL, EMPTY}, 
    {EMPTY, EMPTY, EMPTY, WALL, PATH, HOLE, TP,   PATH, WALL, EMPTY}, 
    {EMPTY, EMPTY, EMPTY, WALL, WALL, WALL, WALL, WALL, WALL, EMPTY}
};

typedef struct {
    int row;
    int col;
} Coord;

typedef struct {
    char tab[];
    int size;
    int start;
    int end;
} queue;

void newQueue

int solveur(int tab[], Coord player){

}
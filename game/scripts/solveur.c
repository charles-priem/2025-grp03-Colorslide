#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdbool.h>

#define EMPTY -1
#define VISITED 0
#define PATH 1
#define WALL 2
#define TP 3
#define HOLE 4
#define PLAYER 5

const int dir_row[4] = { -1, 1, 0, 0 };
const int dir_col[4] = { 0, 0, -1, 1 };
const char dir_chars[4] = { 'N', 'S', 'O', 'E' };

typedef struct {
    int pos;
    char* grid;
    char* moves;
} State;

typedef struct QueueNode {
    State state;
    struct QueueNode* next;
} QueueNode;

typedef struct {
    QueueNode* front;
    QueueNode* rear;
} Queue;

typedef struct {
    unsigned long* keys;
    size_t capacity;
    size_t size;
} VisitedSet;

Queue* createQueue() {
    Queue* q = (Queue*)malloc(sizeof(Queue));
    if (q == NULL) {
        printf("Memory allocation failed for queue\n");
        exit(EXIT_FAILURE);
    }
    q->front = q->rear = NULL;
    return q;
}

bool isEmpty(Queue* q) {
    return q->front == NULL;
}

void enqueue(Queue* q, int pos, char* grid, int grid_size, char* moves, int moves_len) {
    QueueNode* temp = (QueueNode*)malloc(sizeof(QueueNode));
    if (temp == NULL) {
        printf("Memory allocation failed for queue node\n");
        exit(EXIT_FAILURE);
    }

    temp->state.pos = pos;

    temp->state.grid = (char*)malloc(grid_size * sizeof(char));
    if (temp->state.grid == NULL) {
        printf("Memory allocation failed for grid\n");
        exit(EXIT_FAILURE);
    }
    memcpy(temp->state.grid, grid, grid_size * sizeof(char));

    temp->state.moves = (char*)malloc((moves_len + 1) * sizeof(char));
    if (temp->state.moves == NULL) {
        printf("Memory allocation failed for moves\n");
        exit(EXIT_FAILURE);
    }
    strcpy(temp->state.moves, moves);

    temp->next = NULL;

    if (q->rear == NULL) {
        q->front = q->rear = temp;
        return;
    }

    q->rear->next = temp;
    q->rear = temp;
}

State dequeue(Queue* q) {
    if (isEmpty(q)) {
        printf("Queue is empty\n");
        exit(EXIT_FAILURE);
    }

    QueueNode* temp = q->front;
    State state = temp->state;

    q->front = q->front->next;

    if (q->front == NULL) {
        q->rear = NULL;
    }

    free(temp);
    return state;
}

unsigned long hash(int pos, char* grid, int grid_size) {
    unsigned long hash = 5381;
    hash = hash * 33 + pos;

    for (int i = 0; i < grid_size; i++) {
        hash = hash * 33 + grid[i];
    }

    return hash;
}

VisitedSet* createVisitedSet(size_t capacity) {
    VisitedSet* set = (VisitedSet*)malloc(sizeof(VisitedSet));
    if (set == NULL) {
        printf("Memory allocation failed for visited set\n");
        exit(EXIT_FAILURE);
    }

    set->capacity = capacity;
    set->size = 0;
    set->keys = (unsigned long*)calloc(capacity, sizeof(unsigned long));
    if (set->keys == NULL) {
        printf("Memory allocation failed for visited set keys\n");
        exit(EXIT_FAILURE);
    }

    return set;
}

bool contains(VisitedSet* set, unsigned long key) {
    size_t index = key % set->capacity;
    size_t original_index = index;

    do {
        if (set->keys[index] == key) {
            return true;
        }
        if (set->keys[index] == 0) {
            return false;
        }
        index = (index + 1) % set->capacity;
    } while (index != original_index);

    return false;
}

void add(VisitedSet* set, unsigned long key) {
    if (set->size >= set->capacity / 2) {
        size_t old_capacity = set->capacity;
        unsigned long* old_keys = set->keys;

        set->capacity *= 2;
        set->keys = (unsigned long*)calloc(set->capacity, sizeof(unsigned long));
        if (set->keys == NULL) {
            printf("Memory allocation failed for visited set keys resize\n");
            exit(EXIT_FAILURE);
        }

        set->size = 0;
        for (size_t i = 0; i < old_capacity; i++) {
            if (old_keys[i] != 0) {
                add(set, old_keys[i]);
            }
        }

        free(old_keys);
    }

    size_t index = key % set->capacity;
    while (set->keys[index] != 0 && set->keys[index] != key) {
        index = (index + 1) % set->capacity;
    }

    if (set->keys[index] == 0) {
        set->keys[index] = key;
        set->size++;
    }
}

void freeVisitedSet(VisitedSet* set) {
    free(set->keys);
    free(set);
}

int loadLevel(int rows, int cols, const char* file, char* tab) {
    int idx = 0, i = 0, nb_tp = 0, pos_player = -1;
    while (file[i] != '\0' && idx < rows * cols) {
        if ((file[i] >= '0' && file[i] <= '9') || file[i] == '-') {
            char tmp[16];
            int j = 0;
            if (file[i] == '3') nb_tp++;
            if (file[i] == '5') pos_player = idx;
            tmp[j++] = file[i++];
            while (file[i] >= '0' && file[i] <= '9' && j < (int)sizeof(tmp) - 1) {
                tmp[j++] = file[i++];
            }
            tmp[j] = '\0';
            tab[idx++] = (char)atoi(tmp);
        }
        else {
            i++;
        }
    }
    if (nb_tp == 1) tab[pos_player] = TP;

    return pos_player;
}

bool isGridSolved(char* grid, int size) {
    for (int i = 0; i < size; i++) {
        if (grid[i] == PATH) {
            return false;
        }
    }
    return true;
}

char* solve(int start, int rows, int cols, char* tokens) {
    int grid_size = rows * cols;
    char* grid = (char*)malloc(grid_size * sizeof(char));
    if (grid == NULL) {
        printf("Memory allocation failed for grid\n");
        exit(EXIT_FAILURE);
    }
    memcpy(grid, tokens, grid_size * sizeof(char));

    int tps[2] = { -1, -1 };
    int tp_count = 0;

    for (int i = 0; i < grid_size; i++) {
        if (grid[i] == TP) {
            if (tp_count < 2) {
                tps[tp_count++] = i;
            }
        }
    }

    Queue* q = createQueue();
    VisitedSet* visited = createVisitedSet(1024);

    char initial_moves[1] = "";
    enqueue(q, start, grid, grid_size, initial_moves, 0);

    char* solution = NULL;

    while (!isEmpty(q)) {
        State state = dequeue(q);
        int pos = state.pos;
        char* g = state.grid;
        char* moves = state.moves;

        if (isGridSolved(g, grid_size)) {
            int moves_len = strlen(moves);
            solution = (char*)malloc((moves_len + 1) * sizeof(char));
            if (solution == NULL) {
                printf("Memory allocation failed for solution\n");
                exit(EXIT_FAILURE);
            }
            strcpy(solution, moves);

            free(g);
            free(moves);
            break;
        }

        unsigned long key = hash(pos, g, grid_size);

        if (contains(visited, key)) {
            free(g);
            free(moves);
            continue;
        }

        add(visited, key);

        int r0 = pos / cols;
        int c0 = pos % cols;

        for (int d = 0; d < 4; d++) {
            int r = r0, c = c0;
            char* g_copy = (char*)malloc(grid_size * sizeof(char));
            if (g_copy == NULL) {
                printf("Memory allocation failed for grid copy\n");
                exit(EXIT_FAILURE);
            }
            memcpy(g_copy, g, grid_size * sizeof(char));

            bool moved = false;
            bool fell_in_hole = false;

            while (true) {
                int nr = r + dir_row[d];
                int nc = c + dir_col[d];

                if (nr < 0 || nr >= rows || nc < 0 || nc >= cols) break;

                int idx = nr * cols + nc;
                char cell = g_copy[idx];

                if (cell == WALL) break;

                if (cell == TP) {
                    if (tp_count == 2) {
                        int other = (tps[0] == idx) ? tps[1] : tps[0];
                        r = other / cols;
                        c = other % cols;
                        moved = true;
                        continue;
                    }
                }

                if (cell == HOLE) {
                    fell_in_hole = true;
                    break;
                }

                if (cell == PATH)  g_copy[idx] = VISITED;

                r = nr;
                c = nc;
                moved = true;
            }

            if (!moved || fell_in_hole) {
                free(g_copy);
                continue;
            }

            int new_pos = r * cols + c;

            int moves_len = strlen(moves);
            char* new_moves = (char*)malloc((moves_len + 2) * sizeof(char));
            if (new_moves == NULL) {
                printf("Memory allocation failed for new moves\n");
                exit(EXIT_FAILURE);
            }
            strcpy(new_moves, moves);
            new_moves[moves_len] = dir_chars[d];
            new_moves[moves_len + 1] = '\0';

            enqueue(q, new_pos, g_copy, grid_size, new_moves, moves_len + 1);

            free(g_copy);
            free(new_moves);
        }

        free(g);
        free(moves);
    }

    while (!isEmpty(q)) {
        State state = dequeue(q);
        free(state.grid);
        free(state.moves);
    }
    free(q);
    freeVisitedSet(visited);
    free(grid);

    return solution;
}
int main(int argc, char* argv[]) {
    if (argc < 4) {
        printf("Usage: %s rows cols data_string\n", argv[0]);
        return EXIT_FAILURE;
    }

    int rows = atoi(argv[1]);
    int cols = atoi(argv[2]);
    const char* file = argv[3];

    char* playground = (char*)malloc(rows * cols * sizeof(char));
    if (playground == NULL) {
        printf("Memory allocation failed for playground\n");
        return EXIT_FAILURE;
    }

    int player_pos = loadLevel(rows, cols, file, playground);
    char* sol = solve(player_pos, rows, cols, playground);

    if (sol) {
        printf("%s", sol);
        free(sol);
    }
    else {
        printf("No solution found\n");
    }

    free(playground);
    return EXIT_SUCCESS;
}
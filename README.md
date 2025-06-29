# Tic-Tac-Toe with AI backend example

The repository contains an example application for demonstration/educational purpose.

I designed the application and replaced the other person with an AI model.
I created a frontend using JavaScript and HTML. The frontend communicates with the backend, which is written in PHP, via an API.
The backend generates a prompt, processes the AI's response, and sends it to the frontend to display the result.

- docker compose up
- open the following page in your browser: <http://localhost:8080>

## Game Rules

The game is played by two people who take turns marking on X or an O on a 3x3 grid.
The first person to mark three of their signs in a horizontal, vertical, or diagonal row is the winner.

## Testing
For backend run: composer test \
For frontend run: npm run test-win or npm run test-unix

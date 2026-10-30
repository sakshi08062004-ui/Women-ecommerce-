pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                echo 'Building the application...'
                // Docker image build
                sh 'docker build -t women-ecommerce:latest .'
            }
        }

        stage('Test') {
            steps {
                echo 'Running tests...'
                // PHP syntax linting
                sh 'find . -name "*.php" -exec php -l {} \\;'
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploying the application...'
                // Run containers with Docker Compose
                sh 'docker compose up -d'
            }
        }
    }
}

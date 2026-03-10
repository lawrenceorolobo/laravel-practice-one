<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class AssessmentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Use a system user ID (admin) or create a template user
        $userId = DB::table('users')->first()?->id;
        if (!$userId) {
            $this->command->error('No users found. Create a user first.');
            return;
        }

        $templates = $this->getTemplates();

        foreach ($templates as $template) {
            $assessment = Assessment::create([
                'user_id' => $userId,
                'title' => $template['title'],
                'description' => $template['description'],
                'duration_minutes' => $template['duration'],
                'pass_percentage' => $template['pass_pct'],
                'status' => 'draft',
                'is_template' => true,
                'allow_back_navigation' => true,
                'shuffle_questions' => true,
                'shuffle_options' => true,
            ]);

            foreach ($template['questions'] as $i => $q) {
                $question = Question::create([
                    'assessment_id' => $assessment->id,
                    'question_text' => $q['text'],
                    'question_type' => $q['type'],
                    'points' => $q['points'] ?? 1,
                    'question_order' => $i + 1,
                    'expected_answer' => $q['expected_answer'] ?? null,
                    'question_metadata' => isset($q['metadata']) ? json_encode($q['metadata']) : null,
                ]);

                if (!empty($q['options'])) {
                    foreach ($q['options'] as $j => $opt) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $opt['text'],
                            'option_label' => chr(65 + $j), // A, B, C, D...
                            'is_correct' => $opt['correct'] ?? false,
                            'option_order' => $j,
                        ]);
                    }
                }
            }

            $this->command->info("✅ Created: {$template['title']} ({$assessment->questions()->count()} questions)");
        }
    }

    private function getTemplates(): array
    {
        return [
            // ═══════════════════════════════════════════
            // 1. PETROLEUM & OIL/GAS SAFETY
            // ═══════════════════════════════════════════
            [
                'title' => 'Petroleum Operations Safety Assessment',
                'description' => 'Comprehensive safety assessment for petroleum industry workers covering HSE regulations, drilling operations, well control, and emergency procedures.',
                'duration' => 60,
                'pass_pct' => 70,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the primary purpose of a Blowout Preventer (BOP)?', 'options' => [
                        ['text' => 'To prevent uncontrolled release of crude oil or natural gas from a well', 'correct' => true],
                        ['text' => 'To regulate flow rate during normal production', 'correct' => false],
                        ['text' => 'To measure downhole pressure readings', 'correct' => false],
                        ['text' => 'To filter impurities from extracted petroleum', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What does H₂S stand for in oil and gas operations?', 'options' => [
                        ['text' => 'Hydrogen sulfide — a toxic, flammable gas', 'correct' => true],
                        ['text' => 'High-pressure sulfur — a drilling fluid additive', 'correct' => false],
                        ['text' => 'Hydraulic safety system — a well control mechanism', 'correct' => false],
                        ['text' => 'Heat-stable salt — a refinery byproduct', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'At what concentration does H₂S become immediately dangerous to life and health (IDLH)?', 'options' => [
                        ['text' => '100 ppm', 'correct' => true],
                        ['text' => '10 ppm', 'correct' => false],
                        ['text' => '500 ppm', 'correct' => false],
                        ['text' => '1000 ppm', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'A Permit to Work (PTW) system is optional for hot work operations on offshore platforms.', 'options' => [
                        ['text' => 'True', 'correct' => false],
                        ['text' => 'False', 'correct' => true],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What is the correct emergency action when an H₂S alarm sounds?', 'options' => [
                        ['text' => 'Move upwind to the designated muster point', 'correct' => true],
                        ['text' => 'Move downwind to avoid the gas cloud', 'correct' => false],
                        ['text' => 'Remain at your work station and await instructions', 'correct' => false],
                        ['text' => 'Attempt to locate and plug the leak source', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What is the purpose of a SIMOPS (Simultaneous Operations) assessment?', 'options' => [
                        ['text' => 'To identify and mitigate risks when multiple activities occur simultaneously', 'correct' => true],
                        ['text' => 'To schedule production activities in sequence', 'correct' => false],
                        ['text' => 'To simulate emergency evacuation procedures', 'correct' => false],
                        ['text' => 'To test equipment redundancy systems', 'correct' => false],
                    ]],
                    ['type' => 'multiple_choice', 'text' => 'Which of the following are types of well control situations? (Select all that apply)', 'options' => [
                        ['text' => 'Kick', 'correct' => true],
                        ['text' => 'Blowout', 'correct' => true],
                        ['text' => 'Surface overflow', 'correct' => true],
                        ['text' => 'Routine circulation', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What color is the hard hat typically worn by safety personnel on offshore platforms?', 'options' => [
                        ['text' => 'Green', 'correct' => true],
                        ['text' => 'Red', 'correct' => false],
                        ['text' => 'White', 'correct' => false],
                        ['text' => 'Blue', 'correct' => false],
                    ]],
                    ['type' => 'ordering', 'text' => 'Arrange the following steps for well kick detection in the correct order.', 'options' => [
                        ['text' => 'Observe pit gain or flow rate increase', 'correct' => true],
                        ['text' => 'Pick up off bottom', 'correct' => true],
                        ['text' => 'Close the BOP', 'correct' => true],
                        ['text' => 'Record shut-in pressures', 'correct' => true],
                        ['text' => 'Notify company representative', 'correct' => true],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The minimum oxygen level required for safe entry into a confined space is ___ percent.', 'expected_answer' => '19.5||19.5%'],
                    ['type' => 'single_choice', 'text' => 'What is LOTO in the context of petroleum industry safety?', 'options' => [
                        ['text' => 'Lock Out / Tag Out — isolation of energy sources', 'correct' => true],
                        ['text' => 'Log Out / Turn Off — computer system security', 'correct' => false],
                        ['text' => 'Lower Output / Test Operation — production control', 'correct' => false],
                        ['text' => 'Lift Out / Take Over — equipment maintenance', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What is the primary role of a mud engineer on a drilling rig?', 'options' => [
                        ['text' => 'To maintain the drilling fluid properties for wellbore stability and pressure control', 'correct' => true],
                        ['text' => 'To clean mud from the drill floor after operations', 'correct' => false],
                        ['text' => 'To monitor environmental waste disposal', 'correct' => false],
                        ['text' => 'To operate the mud pump systems', 'correct' => false],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 2. SOFTWARE ENGINEERING
            // ═══════════════════════════════════════════
            [
                'title' => 'Software Engineering Technical Assessment',
                'description' => 'Covers algorithms, data structures, system design, databases, APIs, security, and best practices for software engineers.',
                'duration' => 45,
                'pass_pct' => 65,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the time complexity of binary search on a sorted array?', 'options' => [
                        ['text' => 'O(log n)', 'correct' => true],
                        ['text' => 'O(n)', 'correct' => false],
                        ['text' => 'O(n²)', 'correct' => false],
                        ['text' => 'O(1)', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which data structure uses LIFO (Last In, First Out) principle?', 'options' => [
                        ['text' => 'Stack', 'correct' => true],
                        ['text' => 'Queue', 'correct' => false],
                        ['text' => 'Linked List', 'correct' => false],
                        ['text' => 'Hash Map', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'In REST API design, which HTTP method should be used to partially update a resource?', 'options' => [
                        ['text' => 'PATCH', 'correct' => true],
                        ['text' => 'PUT', 'correct' => false],
                        ['text' => 'POST', 'correct' => false],
                        ['text' => 'UPDATE', 'correct' => false],
                    ]],
                    ['type' => 'multiple_choice', 'text' => 'Which of the following are NoSQL database types? (Select all)', 'options' => [
                        ['text' => 'Document store (MongoDB)', 'correct' => true],
                        ['text' => 'Key-value store (Redis)', 'correct' => true],
                        ['text' => 'Column-family store (Cassandra)', 'correct' => true],
                        ['text' => 'Relational store (PostgreSQL)', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What does SOLID stand for in software design principles?', 'options' => [
                        ['text' => 'Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion', 'correct' => true],
                        ['text' => 'Simple, Organized, Logical, Integrated, Documented', 'correct' => false],
                        ['text' => 'Structured, Object-oriented, Layered, Iterative, Deployable', 'correct' => false],
                        ['text' => 'Secure, Optimized, Lean, Isolated, Distributed', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'SQL injection can be prevented by using prepared statements with parameterized queries.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'code_snippet', 'text' => 'Write a function in any language that reverses a string without using built-in reverse methods.', 'points' => 3],
                    ['type' => 'single_choice', 'text' => 'What is the difference between a process and a thread?', 'options' => [
                        ['text' => 'A process has its own memory space; threads share memory within a process', 'correct' => true],
                        ['text' => 'A thread is always faster than a process', 'correct' => false],
                        ['text' => 'Processes can only run on a single core', 'correct' => false],
                        ['text' => 'There is no practical difference', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What HTTP status code indicates "resource created successfully"?', 'options' => [
                        ['text' => '201 Created', 'correct' => true],
                        ['text' => '200 OK', 'correct' => false],
                        ['text' => '204 No Content', 'correct' => false],
                        ['text' => '301 Moved Permanently', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which design pattern ensures a class has only one instance?', 'options' => [
                        ['text' => 'Singleton', 'correct' => true],
                        ['text' => 'Factory', 'correct' => false],
                        ['text' => 'Observer', 'correct' => false],
                        ['text' => 'Adapter', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The CAP theorem states that a distributed system cannot simultaneously guarantee Consistency, Availability, and ___.', 'expected_answer' => 'Partition Tolerance||partition tolerance||Partition tolerance'],
                    ['type' => 'numeric', 'text' => 'If a hash table has a load factor of 0.75 and 12 elements, how many buckets does it have?', 'expected_answer' => '16', 'metadata' => ['tolerance' => 0]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 3. PATTERN RECOGNITION & IQ
            // ═══════════════════════════════════════════
            [
                'title' => 'Pattern Recognition & Cognitive Assessment',
                'description' => 'Tests abstract reasoning, pattern recognition, spatial intelligence, sequence completion, and logical deduction. Similar to IQ and psychometric tests.',
                'duration' => 30,
                'pass_pct' => 60,
                'questions' => [
                    ['type' => 'sequence_pattern', 'text' => 'What comes next in the sequence: 2, 6, 18, 54, ?', 'options' => [
                        ['text' => '162', 'correct' => true],
                        ['text' => '108', 'correct' => false],
                        ['text' => '148', 'correct' => false],
                        ['text' => '216', 'correct' => false],
                    ], 'metadata' => ['pattern_rule' => 'multiply by 3']],
                    ['type' => 'sequence_pattern', 'text' => 'Complete the sequence: 1, 1, 2, 3, 5, 8, ?', 'options' => [
                        ['text' => '13', 'correct' => true],
                        ['text' => '11', 'correct' => false],
                        ['text' => '15', 'correct' => false],
                        ['text' => '10', 'correct' => false],
                    ], 'metadata' => ['pattern_rule' => 'Fibonacci sequence']],
                    ['type' => 'sequence_pattern', 'text' => 'What is the next number: 3, 7, 15, 31, 63, ?', 'options' => [
                        ['text' => '127', 'correct' => true],
                        ['text' => '125', 'correct' => false],
                        ['text' => '96', 'correct' => false],
                        ['text' => '64', 'correct' => false],
                    ], 'metadata' => ['pattern_rule' => 'n*2+1']],
                    ['type' => 'odd_one_out', 'text' => 'Which does NOT belong in this group: Triangle, Square, Circle, Hexagon, Pentagon?', 'options' => [
                        ['text' => 'Circle', 'correct' => true],
                        ['text' => 'Triangle', 'correct' => false],
                        ['text' => 'Square', 'correct' => false],
                        ['text' => 'Pentagon', 'correct' => false],
                    ]],
                    ['type' => 'analogy', 'text' => 'CLOCK is to TIME as THERMOMETER is to:', 'options' => [
                        ['text' => 'Temperature', 'correct' => true],
                        ['text' => 'Mercury', 'correct' => false],
                        ['text' => 'Weather', 'correct' => false],
                        ['text' => 'Celsius', 'correct' => false],
                    ]],
                    ['type' => 'analogy', 'text' => 'AUTHOR is to BOOK as COMPOSER is to:', 'options' => [
                        ['text' => 'Symphony', 'correct' => true],
                        ['text' => 'Piano', 'correct' => false],
                        ['text' => 'Orchestra', 'correct' => false],
                        ['text' => 'Stage', 'correct' => false],
                    ]],
                    ['type' => 'mental_maths', 'text' => 'If a shirt costs ₦4,500 and is discounted by 20%, what do you pay?', 'expected_answer' => '3600', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'mental_maths', 'text' => 'A train travels 240 km in 3 hours. What is its speed in km/h?', 'expected_answer' => '80', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'word_problem', 'text' => 'A factory produces 450 units per day. If demand increases by 15%, how many units must it produce daily to meet the new demand?', 'expected_answer' => '517.5||518||517'],
                    ['type' => 'sequence_pattern', 'text' => 'What comes next: A1, C3, E5, G7, ?', 'options' => [
                        ['text' => 'I9', 'correct' => true],
                        ['text' => 'H8', 'correct' => false],
                        ['text' => 'J10', 'correct' => false],
                        ['text' => 'I8', 'correct' => false],
                    ], 'metadata' => ['pattern_rule' => 'Letter +2, Number +2']],
                    ['type' => 'odd_one_out', 'text' => 'Which word does NOT belong: Swift, Python, Java, Excel, Rust?', 'options' => [
                        ['text' => 'Excel', 'correct' => true],
                        ['text' => 'Swift', 'correct' => false],
                        ['text' => 'Java', 'correct' => false],
                        ['text' => 'Rust', 'correct' => false],
                    ]],
                    ['type' => 'pattern_recognition', 'text' => 'In a 3×3 grid, each row follows a pattern. Row 1: ●○●, Row 2: ○●○, Row 3: ?', 'options' => [
                        ['text' => '●○●', 'correct' => true],
                        ['text' => '○●○', 'correct' => false],
                        ['text' => '●●●', 'correct' => false],
                        ['text' => '○○○', 'correct' => false],
                    ], 'metadata' => ['pattern_rule' => 'alternating rows']],
                ],
            ],

            // ═══════════════════════════════════════════
            // 4. ELECTRICAL ENGINEERING
            // ═══════════════════════════════════════════
            [
                'title' => 'Electrical Engineering Fundamentals',
                'description' => 'Assessment covering circuit analysis, power systems, electrical safety, motors, transformers, and control systems.',
                'duration' => 45,
                'pass_pct' => 65,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'According to Ohm\'s Law, what is the current through a 10Ω resistor with 50V applied?', 'options' => [
                        ['text' => '5 Amperes', 'correct' => true],
                        ['text' => '500 Amperes', 'correct' => false],
                        ['text' => '0.2 Amperes', 'correct' => false],
                        ['text' => '60 Amperes', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What type of motor is commonly used in electric vehicles?', 'options' => [
                        ['text' => 'Permanent Magnet Synchronous Motor (PMSM)', 'correct' => true],
                        ['text' => 'Single-phase induction motor', 'correct' => false],
                        ['text' => 'Universal motor', 'correct' => false],
                        ['text' => 'Stepper motor', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'In a three-phase power system, the line voltage is √3 times the phase voltage in a star (Y) connection.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'numeric', 'text' => 'Calculate the total resistance of three 30Ω resistors connected in parallel.', 'expected_answer' => '10', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'single_choice', 'text' => 'What is the purpose of a relay in an electrical circuit?', 'options' => [
                        ['text' => 'To control a high-power circuit using a low-power signal', 'correct' => true],
                        ['text' => 'To convert AC to DC', 'correct' => false],
                        ['text' => 'To measure electrical resistance', 'correct' => false],
                        ['text' => 'To store electrical energy', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'A transformer works on the principle of ___.', 'expected_answer' => 'electromagnetic induction||mutual induction'],
                    ['type' => 'single_choice', 'text' => 'Which color wire is the earth/ground in standard IEC wiring?', 'options' => [
                        ['text' => 'Green and yellow striped', 'correct' => true],
                        ['text' => 'Blue', 'correct' => false],
                        ['text' => 'Brown', 'correct' => false],
                        ['text' => 'Black', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What does a capacitor store?', 'options' => [
                        ['text' => 'Electrical energy in an electric field', 'correct' => true],
                        ['text' => 'Electrical energy in a magnetic field', 'correct' => false],
                        ['text' => 'Kinetic energy', 'correct' => false],
                        ['text' => 'Thermal energy', 'correct' => false],
                    ]],
                    ['type' => 'numeric', 'text' => 'What is the power (in Watts) consumed by a device drawing 2A from a 220V supply?', 'expected_answer' => '440', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'single_choice', 'text' => 'Which device protects circuits from overcurrent?', 'options' => [
                        ['text' => 'Circuit breaker / Fuse', 'correct' => true],
                        ['text' => 'Voltmeter', 'correct' => false],
                        ['text' => 'Oscilloscope', 'correct' => false],
                        ['text' => 'Signal generator', 'correct' => false],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 5. MECHANICAL ENGINEERING
            // ═══════════════════════════════════════════
            [
                'title' => 'Mechanical Engineering Assessment',
                'description' => 'Covers thermodynamics, fluid mechanics, materials science, machine design, and manufacturing processes.',
                'duration' => 45,
                'pass_pct' => 65,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the SI unit of stress?', 'options' => [
                        ['text' => 'Pascal (Pa)', 'correct' => true],
                        ['text' => 'Newton (N)', 'correct' => false],
                        ['text' => 'Joule (J)', 'correct' => false],
                        ['text' => 'Watt (W)', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which thermodynamic cycle is used in petrol/gasoline engines?', 'options' => [
                        ['text' => 'Otto cycle', 'correct' => true],
                        ['text' => 'Diesel cycle', 'correct' => false],
                        ['text' => 'Brayton cycle', 'correct' => false],
                        ['text' => 'Rankine cycle', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'Bernoulli\'s equation states that an increase in fluid speed leads to a decrease in pressure.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What type of gear is used to transmit power between perpendicular shafts?', 'options' => [
                        ['text' => 'Bevel gear', 'correct' => true],
                        ['text' => 'Spur gear', 'correct' => false],
                        ['text' => 'Worm gear', 'correct' => false],
                        ['text' => 'Helical gear', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'Young\'s Modulus is the ratio of ___ to strain.', 'expected_answer' => 'stress'],
                    ['type' => 'single_choice', 'text' => 'Which material property describes resistance to permanent deformation?', 'options' => [
                        ['text' => 'Hardness', 'correct' => true],
                        ['text' => 'Ductility', 'correct' => false],
                        ['text' => 'Toughness', 'correct' => false],
                        ['text' => 'Elasticity', 'correct' => false],
                    ]],
                    ['type' => 'numeric', 'text' => 'A force of 500N is applied over an area of 0.25m². Calculate the pressure in Pa.', 'expected_answer' => '2000', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'single_choice', 'text' => 'What is the efficiency of a Carnot engine operating between 600K and 300K?', 'options' => [
                        ['text' => '50%', 'correct' => true],
                        ['text' => '25%', 'correct' => false],
                        ['text' => '75%', 'correct' => false],
                        ['text' => '100%', 'correct' => false],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 6. NURSING & HEALTHCARE
            // ═══════════════════════════════════════════
            [
                'title' => 'Healthcare & Nursing Assessment',
                'description' => 'Clinical nursing assessment covering patient care, pharmacology, emergency response, infection control, and medical terminology.',
                'duration' => 40,
                'pass_pct' => 75,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the normal resting heart rate for adults?', 'options' => [
                        ['text' => '60-100 beats per minute', 'correct' => true],
                        ['text' => '40-60 beats per minute', 'correct' => false],
                        ['text' => '100-120 beats per minute', 'correct' => false],
                        ['text' => '20-40 beats per minute', 'correct' => false],
                    ]],
                    ['type' => 'ordering', 'text' => 'Arrange the BLS (Basic Life Support) steps in correct order:', 'options' => [
                        ['text' => 'Check scene safety', 'correct' => true],
                        ['text' => 'Check responsiveness', 'correct' => true],
                        ['text' => 'Call for help (activate EMS)', 'correct' => true],
                        ['text' => 'Begin chest compressions', 'correct' => true],
                        ['text' => 'Open airway and give breaths', 'correct' => true],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Normal blood pressure for a healthy adult is approximately:', 'options' => [
                        ['text' => '120/80 mmHg', 'correct' => true],
                        ['text' => '140/100 mmHg', 'correct' => false],
                        ['text' => '90/60 mmHg', 'correct' => false],
                        ['text' => '160/110 mmHg', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'Hand hygiene is the single most effective method for preventing healthcare-associated infections.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which route of medication administration has the fastest onset of action?', 'options' => [
                        ['text' => 'Intravenous (IV)', 'correct' => true],
                        ['text' => 'Oral', 'correct' => false],
                        ['text' => 'Intramuscular (IM)', 'correct' => false],
                        ['text' => 'Subcutaneous', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'Normal body temperature is approximately ___ degrees Celsius.', 'expected_answer' => '37||36.5||37.0'],
                    ['type' => 'single_choice', 'text' => 'The Glasgow Coma Scale assesses:', 'options' => [
                        ['text' => 'Level of consciousness', 'correct' => true],
                        ['text' => 'Pain intensity', 'correct' => false],
                        ['text' => 'Nutritional status', 'correct' => false],
                        ['text' => 'Wound healing progress', 'correct' => false],
                    ]],
                    ['type' => 'numeric', 'text' => 'If a patient needs 1000mL of IV fluid over 8 hours, what is the flow rate in mL/hour?', 'expected_answer' => '125', 'metadata' => ['tolerance' => 0]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 7. GENERAL KNOWLEDGE & APTITUDE
            // ═══════════════════════════════════════════
            [
                'title' => 'General Knowledge & Aptitude Test',
                'description' => 'Broad assessment covering general knowledge, verbal reasoning, numerical aptitude, and logical deduction.',
                'duration' => 30,
                'pass_pct' => 60,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the chemical symbol for gold?', 'options' => [
                        ['text' => 'Au', 'correct' => true],
                        ['text' => 'Ag', 'correct' => false],
                        ['text' => 'Go', 'correct' => false],
                        ['text' => 'Gd', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which planet is known as the Red Planet?', 'options' => [
                        ['text' => 'Mars', 'correct' => true],
                        ['text' => 'Jupiter', 'correct' => false],
                        ['text' => 'Venus', 'correct' => false],
                        ['text' => 'Saturn', 'correct' => false],
                    ]],
                    ['type' => 'analogy', 'text' => 'DOCTOR is to HOSPITAL as TEACHER is to:', 'options' => [
                        ['text' => 'School', 'correct' => true],
                        ['text' => 'Book', 'correct' => false],
                        ['text' => 'Student', 'correct' => false],
                        ['text' => 'Knowledge', 'correct' => false],
                    ]],
                    ['type' => 'mental_maths', 'text' => 'If 3 workers can complete a job in 12 days, how many days will 4 workers take?', 'expected_answer' => '9'],
                    ['type' => 'sequence_pattern', 'text' => 'What comes next: 100, 81, 64, 49, 36, ?', 'options' => [
                        ['text' => '25', 'correct' => true],
                        ['text' => '24', 'correct' => false],
                        ['text' => '16', 'correct' => false],
                        ['text' => '30', 'correct' => false],
                    ], 'metadata' => ['pattern_rule' => 'Perfect squares descending: 10²,9²,8²,7²,6²,5²']],
                    ['type' => 'true_false', 'text' => 'The Great Wall of China is visible from space with the naked eye.', 'options' => [
                        ['text' => 'True', 'correct' => false],
                        ['text' => 'False', 'correct' => true],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which country has the largest population?', 'options' => [
                        ['text' => 'India', 'correct' => true],
                        ['text' => 'China', 'correct' => false],
                        ['text' => 'United States', 'correct' => false],
                        ['text' => 'Indonesia', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The speed of light is approximately ___ km/s.', 'expected_answer' => '300000||300,000||3×10⁸'],
                ],
            ],

            // ═══════════════════════════════════════════
            // 8. CYBERSECURITY
            // ═══════════════════════════════════════════
            [
                'title' => 'Cybersecurity Fundamentals Assessment',
                'description' => 'Assessment covering network security, cryptography, threat analysis, incident response, and security best practices.',
                'duration' => 40,
                'pass_pct' => 70,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What type of attack involves sending a fraudulent email pretending to be from a trusted source?', 'options' => [
                        ['text' => 'Phishing', 'correct' => true],
                        ['text' => 'DDoS', 'correct' => false],
                        ['text' => 'SQL Injection', 'correct' => false],
                        ['text' => 'Man-in-the-Middle', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which encryption standard is currently recommended for securing data at rest?', 'options' => [
                        ['text' => 'AES-256', 'correct' => true],
                        ['text' => 'DES', 'correct' => false],
                        ['text' => 'MD5', 'correct' => false],
                        ['text' => 'ROT13', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'A firewall alone is sufficient to protect a network from all cyber threats.', 'options' => [
                        ['text' => 'True', 'correct' => false],
                        ['text' => 'False', 'correct' => true],
                    ]],
                    ['type' => 'multiple_choice', 'text' => 'Which of the following are types of malware? (Select all)', 'options' => [
                        ['text' => 'Ransomware', 'correct' => true],
                        ['text' => 'Trojan horse', 'correct' => true],
                        ['text' => 'Worm', 'correct' => true],
                        ['text' => 'Firewall', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What does HTTPS use to secure communications?', 'options' => [
                        ['text' => 'TLS/SSL encryption', 'correct' => true],
                        ['text' => 'VPN tunneling', 'correct' => false],
                        ['text' => 'MAC address filtering', 'correct' => false],
                        ['text' => 'IP whitelisting', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which OWASP Top 10 vulnerability involves improper input validation?', 'options' => [
                        ['text' => 'Injection', 'correct' => true],
                        ['text' => 'Broken Authentication', 'correct' => false],
                        ['text' => 'Security Misconfiguration', 'correct' => false],
                        ['text' => 'Insufficient Logging', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The CIA triad in cybersecurity stands for Confidentiality, Integrity, and ___.', 'expected_answer' => 'Availability||availability'],
                    ['type' => 'ordering', 'text' => 'Arrange the incident response phases in correct order:', 'options' => [
                        ['text' => 'Preparation', 'correct' => true],
                        ['text' => 'Detection and Analysis', 'correct' => true],
                        ['text' => 'Containment', 'correct' => true],
                        ['text' => 'Eradication and Recovery', 'correct' => true],
                        ['text' => 'Post-Incident Review', 'correct' => true],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 9. PROJECT MANAGEMENT
            // ═══════════════════════════════════════════
            [
                'title' => 'Project Management Professional Assessment',
                'description' => 'Covers project planning, risk management, Agile/Scrum, stakeholder management, budgeting, and quality control.',
                'duration' => 35,
                'pass_pct' => 70,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What does the acronym SMART stand for in goal setting?', 'options' => [
                        ['text' => 'Specific, Measurable, Achievable, Relevant, Time-bound', 'correct' => true],
                        ['text' => 'Simple, Manageable, Actionable, Realistic, Trackable', 'correct' => false],
                        ['text' => 'Strategic, Meaningful, Aligned, Resourced, Targeted', 'correct' => false],
                        ['text' => 'Structured, Monitored, Assigned, Reviewed, Tested', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'In Scrum, what is a Sprint?', 'options' => [
                        ['text' => 'A fixed-length iteration (usually 2-4 weeks) to deliver a product increment', 'correct' => true],
                        ['text' => 'A meeting to discuss project risks', 'correct' => false],
                        ['text' => 'A document listing all requirements', 'correct' => false],
                        ['text' => 'The final deployment phase', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'The critical path in a project is the longest sequence of dependent tasks.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Who is responsible for the product backlog in Scrum?', 'options' => [
                        ['text' => 'Product Owner', 'correct' => true],
                        ['text' => 'Scrum Master', 'correct' => false],
                        ['text' => 'Development Team', 'correct' => false],
                        ['text' => 'Project Sponsor', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What is the purpose of a Gantt chart?', 'options' => [
                        ['text' => 'To visualize project schedule and task dependencies', 'correct' => true],
                        ['text' => 'To track project budget variances', 'correct' => false],
                        ['text' => 'To assess team performance', 'correct' => false],
                        ['text' => 'To document requirements', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The three constraints of project management are scope, time, and ___.', 'expected_answer' => 'cost||budget||Cost'],
                    ['type' => 'ordering', 'text' => 'Arrange the project lifecycle phases in order:', 'options' => [
                        ['text' => 'Initiation', 'correct' => true],
                        ['text' => 'Planning', 'correct' => true],
                        ['text' => 'Execution', 'correct' => true],
                        ['text' => 'Monitoring & Controlling', 'correct' => true],
                        ['text' => 'Closing', 'correct' => true],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 10. DATA SCIENCE & ANALYTICS
            // ═══════════════════════════════════════════
            [
                'title' => 'Data Science & Analytics Assessment',
                'description' => 'Covers statistics, machine learning, data visualization, SQL, Python/R, and business analytics concepts.',
                'duration' => 40,
                'pass_pct' => 65,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the difference between supervised and unsupervised learning?', 'options' => [
                        ['text' => 'Supervised uses labeled data; unsupervised finds patterns in unlabeled data', 'correct' => true],
                        ['text' => 'Supervised is faster than unsupervised', 'correct' => false],
                        ['text' => 'Unsupervised requires more training data', 'correct' => false],
                        ['text' => 'There is no practical difference', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which metric is most appropriate for evaluating an imbalanced classification problem?', 'options' => [
                        ['text' => 'F1 Score', 'correct' => true],
                        ['text' => 'Accuracy', 'correct' => false],
                        ['text' => 'Mean Squared Error', 'correct' => false],
                        ['text' => 'R-squared', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What does the p-value represent in hypothesis testing?', 'options' => [
                        ['text' => 'The probability of observing results as extreme as the data, assuming the null hypothesis is true', 'correct' => true],
                        ['text' => 'The probability that the alternative hypothesis is correct', 'correct' => false],
                        ['text' => 'The power of the statistical test', 'correct' => false],
                        ['text' => 'The sample size required', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'Correlation always implies causation.', 'options' => [
                        ['text' => 'True', 'correct' => false],
                        ['text' => 'False', 'correct' => true],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What technique is used to prevent overfitting in decision trees?', 'options' => [
                        ['text' => 'Pruning', 'correct' => true],
                        ['text' => 'Boosting the learning rate', 'correct' => false],
                        ['text' => 'Adding more features', 'correct' => false],
                        ['text' => 'Removing training data', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The arithmetic mean of 10, 20, 30, 40, 50 is ___.', 'expected_answer' => '30'],
                    ['type' => 'numeric', 'text' => 'If a dataset has values [2, 4, 4, 6, 8], what is the median?', 'expected_answer' => '4', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'single_choice', 'text' => 'Which SQL clause is used to filter groups of aggregated data?', 'options' => [
                        ['text' => 'HAVING', 'correct' => true],
                        ['text' => 'WHERE', 'correct' => false],
                        ['text' => 'GROUP BY', 'correct' => false],
                        ['text' => 'ORDER BY', 'correct' => false],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 11. CIVIL ENGINEERING
            // ═══════════════════════════════════════════
            [
                'title' => 'Civil Engineering Assessment',
                'description' => 'Covers structural analysis, geotechnical engineering, concrete design, construction management, and surveying.',
                'duration' => 45,
                'pass_pct' => 65,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'What is the water-cement ratio that provides maximum workability and strength for M25 concrete?', 'options' => [
                        ['text' => '0.45', 'correct' => true],
                        ['text' => '0.65', 'correct' => false],
                        ['text' => '0.25', 'correct' => false],
                        ['text' => '0.85', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'What is the standard curing period for concrete?', 'options' => [
                        ['text' => '28 days', 'correct' => true],
                        ['text' => '7 days', 'correct' => false],
                        ['text' => '14 days', 'correct' => false],
                        ['text' => '56 days', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'Which soil type generally has the highest bearing capacity?', 'options' => [
                        ['text' => 'Rock', 'correct' => true],
                        ['text' => 'Clay', 'correct' => false],
                        ['text' => 'Sand', 'correct' => false],
                        ['text' => 'Silt', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'Reinforced concrete uses steel bars to resist tensile forces.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The bending moment is zero at points of ___ in a beam.', 'expected_answer' => 'inflection||contraflexure'],
                    ['type' => 'single_choice', 'text' => 'What does SPT stand for in geotechnical engineering?', 'options' => [
                        ['text' => 'Standard Penetration Test', 'correct' => true],
                        ['text' => 'Soil Pressure Test', 'correct' => false],
                        ['text' => 'Structural Performance Test', 'correct' => false],
                        ['text' => 'Surface Paving Technique', 'correct' => false],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 12. ACCOUNTING & FINANCE
            // ═══════════════════════════════════════════
            [
                'title' => 'Accounting & Finance Assessment',
                'description' => 'Covers financial statements, bookkeeping, taxation, auditing, budgeting, and financial analysis.',
                'duration' => 35,
                'pass_pct' => 70,
                'questions' => [
                    ['type' => 'single_choice', 'text' => 'Which financial statement shows a company\'s revenues and expenses over a period?', 'options' => [
                        ['text' => 'Income Statement (Profit & Loss)', 'correct' => true],
                        ['text' => 'Balance Sheet', 'correct' => false],
                        ['text' => 'Cash Flow Statement', 'correct' => false],
                        ['text' => 'Statement of Equity', 'correct' => false],
                    ]],
                    ['type' => 'single_choice', 'text' => 'In double-entry bookkeeping, every transaction affects at least:', 'options' => [
                        ['text' => 'Two accounts', 'correct' => true],
                        ['text' => 'One account', 'correct' => false],
                        ['text' => 'Three accounts', 'correct' => false],
                        ['text' => 'Four accounts', 'correct' => false],
                    ]],
                    ['type' => 'true_false', 'text' => 'Assets = Liabilities + Shareholders\' Equity is the fundamental accounting equation.', 'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ]],
                    ['type' => 'fill_blank', 'text' => 'The process of allocating the cost of tangible assets over their useful life is called ___.', 'expected_answer' => 'depreciation||Depreciation'],
                    ['type' => 'single_choice', 'text' => 'What does ROI stand for?', 'options' => [
                        ['text' => 'Return on Investment', 'correct' => true],
                        ['text' => 'Rate of Interest', 'correct' => false],
                        ['text' => 'Revenue over Income', 'correct' => false],
                        ['text' => 'Ratio of Inflation', 'correct' => false],
                    ]],
                    ['type' => 'numeric', 'text' => 'If total assets are ₦5,000,000 and total liabilities are ₦3,000,000, what is the shareholders\' equity?', 'expected_answer' => '2000000', 'metadata' => ['tolerance' => 0]],
                    ['type' => 'ordering', 'text' => 'Arrange the accounting cycle steps in order:', 'options' => [
                        ['text' => 'Identify transactions', 'correct' => true],
                        ['text' => 'Record in journal entries', 'correct' => true],
                        ['text' => 'Post to ledger', 'correct' => true],
                        ['text' => 'Prepare trial balance', 'correct' => true],
                        ['text' => 'Prepare financial statements', 'correct' => true],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 13. VISUAL ABSTRACT REASONING (SVG Patterns)
            // ═══════════════════════════════════════════
            [
                'title' => 'Visual Abstract Reasoning Assessment',
                'description' => 'Raven\'s-style visual pattern recognition. Candidates identify patterns in sequences of shapes, arrows, and geometric figures rendered as SVG. Tests spatial reasoning, abstract logic, and pattern completion.',
                'duration' => 25,
                'pass_pct' => 60,
                'questions' => [
                    // Q1: Arrow direction sequence (like the user's image — black corner + arrows)
                    ['type' => 'sequence_pattern', 'text' => 'Which square comes next in this sequence?', 'points' => 2, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'sequence',
                        'cells' => [
                            // Cell 1: black triangle bottom-left + diagonal arrow down-left
                            ['shapes' => [
                                ['type' => 'triangle', 'x' => 20, 'y' => 60, 'size' => 40, 'fill' => '#000', 'rotate' => 180, 'cx' => 20, 'cy' => 80],
                                ['type' => 'arrow', 'x1' => 70, 'y1' => 20, 'x2' => 20, 'y2' => 70, 'fill' => '#000', 'sw' => 2],
                            ]],
                            // Cell 2: black triangle bottom-right + horizontal double arrow
                            ['shapes' => [
                                ['type' => 'rect', 'x' => 60, 'y' => 60, 'w' => 40, 'h' => 40, 'fill' => '#000'],
                                ['type' => 'arrow', 'x1' => 15, 'y1' => 50, 'x2' => 85, 'y2' => 50, 'fill' => '#000', 'sw' => 2, 'double' => true],
                            ]],
                            // Cell 3: black triangle top-right
                            ['shapes' => [
                                ['type' => 'rect', 'x' => 60, 'y' => 0, 'w' => 40, 'h' => 40, 'fill' => '#000'],
                                ['type' => 'arrow', 'x1' => 50, 'y1' => 80, 'x2' => 50, 'y2' => 20, 'fill' => '#000', 'sw' => 2],
                            ]],
                            // Cell 4: black triangle top-left + vertical arrow
                            ['shapes' => [
                                ['type' => 'rect', 'x' => 0, 'y' => 0, 'w' => 40, 'h' => 40, 'fill' => '#000'],
                                ['type' => 'arrow', 'x1' => 50, 'y1' => 20, 'x2' => 50, 'y2' => 80, 'fill' => '#000', 'sw' => 2, 'double' => true],
                            ]],
                            // Cell 5: black triangle bottom-left + diagonal arrow NE
                            ['shapes' => [
                                ['type' => 'triangle', 'x' => 20, 'y' => 60, 'size' => 40, 'fill' => '#000', 'rotate' => 180, 'cx' => 20, 'cy' => 80],
                                ['type' => 'arrow', 'x1' => 20, 'y1' => 70, 'x2' => 80, 'y2' => 20, 'fill' => '#000', 'sw' => 2],
                            ]],
                        ],
                        'option_cells' => [
                            // A — correct: horizontal arrow + bottom-right black
                            ['shapes' => [
                                ['type' => 'rect', 'x' => 60, 'y' => 60, 'w' => 40, 'h' => 40, 'fill' => '#000'],
                                ['type' => 'arrow', 'x1' => 15, 'y1' => 50, 'x2' => 85, 'y2' => 50, 'fill' => '#000', 'sw' => 2],
                            ]],
                            // B — vertical arrow only
                            ['shapes' => [
                                ['type' => 'arrow', 'x1' => 50, 'y1' => 20, 'x2' => 50, 'y2' => 80, 'fill' => '#000', 'sw' => 2],
                            ]],
                            // C — double horizontal arrow
                            ['shapes' => [
                                ['type' => 'arrow', 'x1' => 15, 'y1' => 50, 'x2' => 85, 'y2' => 50, 'fill' => '#000', 'sw' => 2, 'double' => true],
                            ]],
                            // D — diagonal + bottom-right black
                            ['shapes' => [
                                ['type' => 'rect', 'x' => 60, 'y' => 60, 'w' => 40, 'h' => 40, 'fill' => '#000'],
                                ['type' => 'arrow', 'x1' => 20, 'y1' => 20, 'x2' => 70, 'y2' => 70, 'fill' => '#000', 'sw' => 2],
                            ]],
                        ],
                    ]]],

                    // Q2: Shape size progression
                    ['type' => 'sequence_pattern', 'text' => 'What comes next? The shapes follow a growth pattern.', 'points' => 2, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'sequence',
                        'cells' => [
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 8, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 14, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 20, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 26, 'fill' => '#3b82f6']]],
                        ],
                        'option_cells' => [
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 32, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 20, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 38, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'rect', 'x' => 20, 'y' => 20, 'w' => 60, 'h' => 60, 'fill' => '#3b82f6']]],
                        ],
                    ]]],

                    // Q3: Matrix pattern — shapes in 3x3 grid
                    ['type' => 'matrix_pattern', 'text' => 'Find the missing piece in the 3×3 matrix.', 'points' => 3, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'matrix',
                        'cells' => [
                            // Row 1: circle, triangle, diamond
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'triangle', 'x' => 50, 'y' => 15, 'size' => 50, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#ef4444']]],
                            // Row 2: same shapes blue
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'triangle', 'x' => 50, 'y' => 15, 'size' => 50, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#3b82f6']]],
                            // Row 3: same shapes green, last one is ?
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#22c55e']]],
                            ['shapes' => [['type' => 'triangle', 'x' => 50, 'y' => 15, 'size' => 50, 'fill' => '#22c55e']]],
                            ['shapes' => []], // This will be replaced by ?
                        ],
                        'option_cells' => [
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#22c55e']]],
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#22c55e']]],
                            ['shapes' => [['type' => 'triangle', 'x' => 50, 'y' => 15, 'size' => 50, 'fill' => '#22c55e']]],
                        ],
                    ]]],

                    // Q4: Rotation pattern
                    ['type' => 'spatial_rotation', 'text' => 'The arrow rotates 90° clockwise each step. What comes next?', 'points' => 2, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'rotation',
                        'cells' => [
                            ['shapes' => [['type' => 'arrow', 'x1' => 50, 'y1' => 80, 'x2' => 50, 'y2' => 20, 'fill' => '#6366f1', 'sw' => 3]]],  // Up
                            ['shapes' => [['type' => 'arrow', 'x1' => 20, 'y1' => 50, 'x2' => 80, 'y2' => 50, 'fill' => '#6366f1', 'sw' => 3]]],  // Right
                            ['shapes' => [['type' => 'arrow', 'x1' => 50, 'y1' => 20, 'x2' => 50, 'y2' => 80, 'fill' => '#6366f1', 'sw' => 3]]],  // Down
                        ],
                        'option_cells' => [
                            ['shapes' => [['type' => 'arrow', 'x1' => 80, 'y1' => 50, 'x2' => 20, 'y2' => 50, 'fill' => '#6366f1', 'sw' => 3]]],  // Left (correct)
                            ['shapes' => [['type' => 'arrow', 'x1' => 50, 'y1' => 80, 'x2' => 50, 'y2' => 20, 'fill' => '#6366f1', 'sw' => 3]]],  // Up
                            ['shapes' => [['type' => 'arrow', 'x1' => 20, 'y1' => 80, 'x2' => 80, 'y2' => 20, 'fill' => '#6366f1', 'sw' => 3]]],  // Diagonal
                            ['shapes' => [['type' => 'arrow', 'x1' => 20, 'y1' => 50, 'x2' => 80, 'y2' => 50, 'fill' => '#6366f1', 'sw' => 3]]],  // Right
                        ],
                    ]]],

                    // Q5: Shape count increasing
                    ['type' => 'sequence_pattern', 'text' => 'How many stars should appear in the next cell?', 'points' => 2, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'sequence',
                        'cells' => [
                            ['shapes' => [['type' => 'star', 'cx' => 50, 'cy' => 50, 'r' => 20, 'fill' => '#eab308']]],
                            ['shapes' => [['type' => 'star', 'cx' => 30, 'cy' => 50, 'r' => 15, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 70, 'cy' => 50, 'r' => 15, 'fill' => '#eab308']]],
                            ['shapes' => [['type' => 'star', 'cx' => 25, 'cy' => 35, 'r' => 12, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 50, 'cy' => 65, 'r' => 12, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 75, 'cy' => 35, 'r' => 12, 'fill' => '#eab308']]],
                        ],
                        'option_cells' => [
                            // A: 4 stars (correct)
                            ['shapes' => [['type' => 'star', 'cx' => 30, 'cy' => 30, 'r' => 10, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 70, 'cy' => 30, 'r' => 10, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 30, 'cy' => 70, 'r' => 10, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 70, 'cy' => 70, 'r' => 10, 'fill' => '#eab308']]],
                            // B: 3 stars
                            ['shapes' => [['type' => 'star', 'cx' => 25, 'cy' => 50, 'r' => 12, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 50, 'cy' => 50, 'r' => 12, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 75, 'cy' => 50, 'r' => 12, 'fill' => '#eab308']]],
                            // C: 5 stars
                            ['shapes' => [['type' => 'star', 'cx' => 50, 'cy' => 20, 'r' => 8, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 25, 'cy' => 40, 'r' => 8, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 75, 'cy' => 40, 'r' => 8, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 35, 'cy' => 70, 'r' => 8, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 65, 'cy' => 70, 'r' => 8, 'fill' => '#eab308']]],
                            // D: 2 stars
                            ['shapes' => [['type' => 'star', 'cx' => 30, 'cy' => 50, 'r' => 15, 'fill' => '#eab308'], ['type' => 'star', 'cx' => 70, 'cy' => 50, 'r' => 15, 'fill' => '#eab308']]],
                        ],
                    ]]],

                    // Q6: Color alternation pattern
                    ['type' => 'sequence_pattern', 'text' => 'Identify the next colored shape in the alternating sequence.', 'points' => 1, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'sequence',
                        'cells' => [
                            ['shapes' => [['type' => 'rect', 'x' => 20, 'y' => 20, 'w' => 60, 'h' => 60, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 28, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'rect', 'x' => 20, 'y' => 20, 'w' => 60, 'h' => 60, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 28, 'fill' => '#3b82f6']]],
                        ],
                        'option_cells' => [
                            ['shapes' => [['type' => 'rect', 'x' => 20, 'y' => 20, 'w' => 60, 'h' => 60, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'circle', 'cx' => 50, 'cy' => 50, 'r' => 28, 'fill' => '#3b82f6']]],
                            ['shapes' => [['type' => 'triangle', 'x' => 50, 'y' => 15, 'size' => 50, 'fill' => '#ef4444']]],
                            ['shapes' => [['type' => 'rect', 'x' => 20, 'y' => 20, 'w' => 60, 'h' => 60, 'fill' => '#3b82f6']]],
                        ],
                    ]]],

                    // Q7: Cross rotation + shape change matrix
                    ['type' => 'matrix_pattern', 'text' => 'Complete the 3×3 matrix. Each row has the same shapes rotating.', 'points' => 3, 'options' => [
                        ['text' => '', 'correct' => true],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                        ['text' => '', 'correct' => false],
                    ], 'metadata' => ['visual_pattern' => [
                        'type' => 'matrix',
                        'cells' => [
                            ['shapes' => [['type' => 'cross', 'cx' => 50, 'cy' => 50, 'r' => 25, 't' => 8, 'fill' => '#8b5cf6']]],
                            ['shapes' => [['type' => 'cross', 'cx' => 50, 'cy' => 50, 'r' => 25, 't' => 8, 'fill' => '#8b5cf6', 'rotate' => 45]]],
                            ['shapes' => [['type' => 'cross', 'cx' => 50, 'cy' => 50, 'r' => 25, 't' => 8, 'fill' => '#8b5cf6', 'rotate' => 90]]],

                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#f97316']]],
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#f97316', 'rotate' => 45]]],
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#f97316', 'rotate' => 90]]],

                            ['shapes' => [['type' => 'star', 'cx' => 50, 'cy' => 50, 'r' => 22, 'fill' => '#22c55e']]],
                            ['shapes' => [['type' => 'star', 'cx' => 50, 'cy' => 50, 'r' => 22, 'fill' => '#22c55e', 'rotate' => 36]]],
                            ['shapes' => []], // ?
                        ],
                        'option_cells' => [
                            ['shapes' => [['type' => 'star', 'cx' => 50, 'cy' => 50, 'r' => 22, 'fill' => '#22c55e', 'rotate' => 72]]],
                            ['shapes' => [['type' => 'star', 'cx' => 50, 'cy' => 50, 'r' => 22, 'fill' => '#ef4444', 'rotate' => 72]]],
                            ['shapes' => [['type' => 'cross', 'cx' => 50, 'cy' => 50, 'r' => 25, 't' => 8, 'fill' => '#22c55e', 'rotate' => 72]]],
                            ['shapes' => [['type' => 'diamond', 'cx' => 50, 'cy' => 50, 'r' => 25, 'fill' => '#22c55e', 'rotate' => 72]]],
                        ],
                    ]]],

                    // Q8-Q10: Simple text/numeric patterns
                    ['type' => 'sequence_pattern', 'text' => 'What comes next: ▲ ■ ● ▲ ■ ?', 'options' => [
                        ['text' => '●', 'correct' => true], ['text' => '▲', 'correct' => false], ['text' => '■', 'correct' => false], ['text' => '◆', 'correct' => false],
                    ]],
                    ['type' => 'pattern_recognition', 'text' => 'If ★ = 3 and ● = 5, what is ★ + ● + ★?', 'options' => [
                        ['text' => '11', 'correct' => true], ['text' => '13', 'correct' => false], ['text' => '8', 'correct' => false], ['text' => '16', 'correct' => false],
                    ]],
                    ['type' => 'spatial_rotation', 'text' => 'A square is rotated 45°. What shape does it appear as?', 'options' => [
                        ['text' => 'Diamond/Rhombus', 'correct' => true], ['text' => 'Rectangle', 'correct' => false], ['text' => 'Triangle', 'correct' => false], ['text' => 'Pentagon', 'correct' => false],
                    ]],
                ],
            ],

            // ═══════════════════════════════════════════
            // 14. SHAPE PUZZLE (Duolingo-style)
            // ═══════════════════════════════════════════
            [
                'title' => 'Shape Puzzle Challenge',
                'description' => 'Duolingo-style drag-and-drop puzzle. Candidates must fit the correct shapes into the correct slots. Tests spatial awareness, logical thinking, and fine motor skills.',
                'duration' => 15,
                'pass_pct' => 70,
                'questions' => [
                    ['type' => 'shape_puzzle', 'text' => 'Drag each shape to its matching slot:', 'options' => [
                        ['text' => '🔴 Circle', 'correct' => true],
                        ['text' => '🟦 Square', 'correct' => true],
                        ['text' => '🔺 Triangle', 'correct' => true],
                    ]],
                    ['type' => 'shape_puzzle', 'text' => 'Match each color to its correct position:', 'options' => [
                        ['text' => 'Red → Position 1', 'correct' => true],
                        ['text' => 'Blue → Position 2', 'correct' => true],
                        ['text' => 'Green → Position 3', 'correct' => true],
                        ['text' => 'Yellow → Position 4', 'correct' => true],
                    ]],
                    ['type' => 'shape_puzzle', 'text' => 'Arrange the puzzle pieces to form a complete square:', 'options' => [
                        ['text' => 'Top-Left piece', 'correct' => true],
                        ['text' => 'Top-Right piece', 'correct' => true],
                        ['text' => 'Bottom-Left piece', 'correct' => true],
                        ['text' => 'Bottom-Right piece', 'correct' => true],
                    ]],
                    ['type' => 'shape_puzzle', 'text' => 'Place each tool in its correct toolbox slot:', 'options' => [
                        ['text' => '🔨 Hammer', 'correct' => true],
                        ['text' => '🔧 Wrench', 'correct' => true],
                        ['text' => '🪛 Screwdriver', 'correct' => true],
                        ['text' => '🪚 Saw', 'correct' => true],
                        ['text' => '📏 Ruler', 'correct' => true],
                    ]],
                    ['type' => 'shape_puzzle', 'text' => 'Fit the correct continent into each position on the world map:', 'options' => [
                        ['text' => 'North America', 'correct' => true],
                        ['text' => 'South America', 'correct' => true],
                        ['text' => 'Europe', 'correct' => true],
                        ['text' => 'Africa', 'correct' => true],
                        ['text' => 'Asia', 'correct' => true],
                    ]],
                    ['type' => 'shape_puzzle', 'text' => 'Sort the fractions from smallest to largest by dragging:', 'options' => [
                        ['text' => '1/4', 'correct' => true],
                        ['text' => '1/3', 'correct' => true],
                        ['text' => '1/2', 'correct' => true],
                        ['text' => '2/3', 'correct' => true],
                        ['text' => '3/4', 'correct' => true],
                    ]],
                ],
            ],
        ];
    }
}

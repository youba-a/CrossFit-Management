const memberPerformanceApp = Vue.createApp({
  data() {
    return {
      performances: [],
      chart: null,
      filterRange: '30',
    }
  },
  computed: {
    filteredPerformances() {
      if (this.filterRange === 'all') return this.performances;
      const days = parseInt(this.filterRange);
      const cutoff = new Date();
      cutoff.setDate(cutoff.getDate() - days);
      return this.performances.filter(p => new Date(p.date_session) >= cutoff);
    },
    averagePerWeek() {
      if (this.filteredPerformances.length === 0) return 0;
      const dates = this.filteredPerformances.map(p => new Date(p.date_session));
      const min = Math.min(...dates);
      const max = Math.max(...dates);
      const weeks = Math.max(1, (max - min) / (1000 * 60 * 60 * 24 * 7));
      return (this.filteredPerformances.length / weeks).toFixed(1);
    },
    favoriteActivity() {
      const counts = {};
      this.filteredPerformances.forEach(p => {
        counts[p.activity_type] = (counts[p.activity_type] || 0) + 1;
      });
      const top = Object.entries(counts).sort((a, b) => b[1] - a[1])[0];
      return top ? top[0] : null;
    },
    lastFeedback() {
      const fb = this.performances.filter(p => p.feedbackCoachOnUser_organisationuser)
        .sort((a, b) => new Date(b.date_session) - new Date(a.date_session));
      return fb.length ? fb[0].feedbackCoachOnUser_organisationuser : null;
    },
    consistencyStreak() {
    if (!this.performances.length) return 0;

    // Extract unique ISO weeks (YYYY-WW)
    const weeks = new Set(
        this.performances.map(p => {
        const d = new Date(p.date_session);
        const oneJan = new Date(d.getFullYear(), 0, 1);
        const week = Math.ceil(((d - oneJan) / 86400000 + oneJan.getDay() + 1) / 7);
        return `${d.getFullYear()}-${week}`;
        })
    );

    // Convert to sorted array
    const sortedWeeks = Array.from(weeks).sort();

    // Find streak starting from current week backwards
    const today = new Date();
    const oneJan = new Date(today.getFullYear(), 0, 1);
    const currentWeek = Math.ceil(((today - oneJan) / 86400000 + oneJan.getDay() + 1) / 7);

    let streak = 0;
    for (let w = currentWeek; w > 0; w--) {
        const key = `${today.getFullYear()}-${w}`;
        if (weeks.has(key)) streak++;
        else break;
    }

    return streak;
    },
  },
  watch: {
    filterRange() { this.renderChart(); }
  },
  mounted() {
    this.fetchPerformance();
  },
  methods: {
    fetchPerformance() {
      fetch('/back/API/member/memberGetPerformance.php', { credentials: 'include' })
        .then(res => res.json())
        .then(data => {
          if (data.error) return;
          this.performances = data;
          this.renderChart();
        });
    },

    getColorFromCode(code) {
      const colorMap = {
        1: "#f9b233",
        2: "#00b0f0",
        3: "#e6007e",
        4: "#3fc380",
        5: "#9b59b6",
        6: "#e74c3c",
        7: "#f1c40f",
        8: "#34495e",
        9: "#1abc9c",
        10: "#bdc3c7",
        default: "#7f8c8d"
      };
      return colorMap[code] || colorMap.default;
    },

    renderChart() {
      if (this.chart) this.chart.destroy();

      const grouped = {};

      this.filteredPerformances.forEach(p => {
        if (!grouped[p.activity_type]) {
          grouped[p.activity_type] = {
            count: 0,
            color: this.getColorFromCode(p.activity_color)
          };
        }
        grouped[p.activity_type].count++;
      });

      const labels = Object.keys(grouped);
      const values = labels.map(l => grouped[l].count);
      const colors = labels.map(l => grouped[l].color);

      const ctx = document.getElementById('performanceChart');
      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Nombre de sessions',
            data: values,
            backgroundColor: colors
          }]
        },
        options: {
          responsive: true,
          plugins: {
            tooltip: {
              callbacks: {
                label: (ctx) => {
                  const type = ctx.label;
                  const sessions = this.filteredPerformances
                    .filter(p => p.activity_type === type)
                    .map(p => {
                      const d = new Date(p.date_session).toLocaleDateString();
                      const fb = p.feedbackCoachOnUser_organisationuser ? ` — 💬 ${p.feedbackCoachOnUser_organisationuser}` : '';
                      return `${d}: ${p.performance_organisationuser} (${p.coach_firstname})${fb}`;
                    });
                  return sessions.join('\n');
                }
              }
            },
            legend: { display: false },
            title: {
              display: true,
              text: 'Sessions par type d’activité'
            }
          },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
          }
        }
      });
    }
  }
});

memberPerformanceApp.mount('#memberPerformanceApp');
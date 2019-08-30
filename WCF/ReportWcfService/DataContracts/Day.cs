using System.Collections.Generic;
using System.Runtime.Serialization;

namespace ReportWcfService
{
    [DataContract]
    public class Day
    {
        [DataMember]
        public DaysOfWeek DayOfWeek { get; set; }

        [DataMember]
        public List<Activity> Activities { get; set; }

        public Day() { }
    }
}